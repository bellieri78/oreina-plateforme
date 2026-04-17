# Chersotis — Import Word/ODT via Claude API — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Remplacer le parsing phpword direct par un appel à Claude Haiku qui convertit le texte brut du document en Markdown structuré, puis passer par `MarkdownToBlocksService` existant.

**Architecture :** `DocumentConversionService` extrait le texte brut du `.docx`/`.odt` via phpword, l'envoie à l'API Anthropic (Claude Haiku) avec un prompt de conversion, retourne du Markdown. Le controller dispatch vers ce service pour les documents Word/ODT et vers `MarkdownToBlocksService` pour les `.md`. L'ancien `DocumentToBlocksService` est supprimé.

**Tech Stack :** Laravel 12, PHPUnit 11, `phpoffice/phpword` (extraction texte), API Anthropic (HTTP direct via `Http::post()`).

**Spec :** `docs/superpowers/specs/2026-04-17-chersotis-import-docx-claude-design.md`

---

## File Structure

### Fichiers créés

| Chemin | Responsabilité |
|---|---|
| `app/Services/DocumentConversionService.php` | Extraction texte phpword + appel Claude API → Markdown |
| `tests/Unit/Services/DocumentConversionServiceTest.php` | Tests TDD avec Http::fake() |

### Fichiers modifiés

| Chemin | Changement |
|---|---|
| `config/services.php` | Ajout section `anthropic` |
| `app/Http/Controllers/Admin/SubmissionController.php` | Remplacer `DocumentToBlocksService` par `DocumentConversionService` |
| `resources/views/admin/documentation/index.blade.php` | Doc mise à jour |

### Fichiers supprimés

| Chemin | Raison |
|---|---|
| `app/Services/DocumentToBlocksService.php` | Remplacé par `DocumentConversionService` |
| `tests/Unit/Services/DocumentToBlocksServiceTest.php` | Tests de l'ancien service |

---

## Task 1: Config + DocumentConversionService (TDD)

**Files:**
- Modify: `config/services.php`
- Create: `tests/Unit/Services/DocumentConversionServiceTest.php`
- Create: `app/Services/DocumentConversionService.php`

---

- [ ] **Step 1.1: Add Anthropic config**

In `config/services.php`, add after the `turnstile` section (before the closing `];`):

```php
    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
    ],
```

- [ ] **Step 1.2: Write tests**

File `tests/Unit/Services/DocumentConversionServiceTest.php`:

```php
<?php

namespace Tests\Unit\Services;

use App\Services\DocumentConversionService;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use Tests\TestCase;

class DocumentConversionServiceTest extends TestCase
{
    private DocumentConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DocumentConversionService();
    }

    private function createDocx(callable $builder): string
    {
        $phpWord = new PhpWord();
        $builder($phpWord);
        $path = tempnam(sys_get_temp_dir(), 'test_') . '.docx';
        IOFactory::createWriter($phpWord, 'Word2007')->save($path);

        return $path;
    }

    private function createOdt(callable $builder): string
    {
        $phpWord = new PhpWord();
        $builder($phpWord);
        $path = tempnam(sys_get_temp_dir(), 'test_') . '.odt';
        IOFactory::createWriter($phpWord, 'ODText')->save($path);

        return $path;
    }

    public function test_extract_text_from_docx(): void
    {
        $path = $this->createDocx(function (PhpWord $pw) {
            $section = $pw->addSection();
            $section->addText('Premier paragraphe');
            $section->addText('Deuxième paragraphe');
        });

        $text = $this->service->extractText($path);
        unlink($path);

        $this->assertStringContainsString('Premier paragraphe', $text);
        $this->assertStringContainsString('Deuxième paragraphe', $text);
    }

    public function test_extract_text_from_odt(): void
    {
        $path = $this->createOdt(function (PhpWord $pw) {
            $section = $pw->addSection();
            $section->addText('Contenu ODT');
        });

        $text = $this->service->extractText($path);
        unlink($path);

        $this->assertStringContainsString('Contenu ODT', $text);
    }

    public function test_extract_text_includes_table_content(): void
    {
        $path = $this->createDocx(function (PhpWord $pw) {
            $section = $pw->addSection();
            $table = $section->addTable();
            $table->addRow();
            $table->addCell()->addText('Espèce');
            $table->addCell()->addText('Localité');
            $table->addRow();
            $table->addCell()->addText('P. apollo');
            $table->addCell()->addText('Pyrénées');
        });

        $text = $this->service->extractText($path);
        unlink($path);

        $this->assertStringContainsString('Espèce', $text);
        $this->assertStringContainsString('P. apollo', $text);
        $this->assertStringContainsString('Pyrénées', $text);
    }

    public function test_extract_text_includes_list_items(): void
    {
        $path = $this->createDocx(function (PhpWord $pw) {
            $section = $pw->addSection();
            $section->addListItem('Premier élément');
            $section->addListItem('Deuxième élément');
        });

        $text = $this->service->extractText($path);
        unlink($path);

        $this->assertStringContainsString('Premier élément', $text);
        $this->assertStringContainsString('Deuxième élément', $text);
    }

    public function test_throws_when_api_key_missing(): void
    {
        config(['services.anthropic.api_key' => null]);

        $path = $this->createDocx(function (PhpWord $pw) {
            $pw->addSection()->addText('Test');
        });

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('API');

        try {
            $this->service->toMarkdown($path);
        } finally {
            unlink($path);
        }
    }

    public function test_throws_when_api_fails(): void
    {
        config(['services.anthropic.api_key' => 'sk-ant-test']);

        Http::fake([
            'api.anthropic.com/*' => Http::response(['error' => ['message' => 'Unauthorized']], 401),
        ]);

        $path = $this->createDocx(function (PhpWord $pw) {
            $pw->addSection()->addText('Test');
        });

        $this->expectException(\RuntimeException::class);

        try {
            $this->service->toMarkdown($path);
        } finally {
            unlink($path);
        }
    }

    public function test_to_markdown_returns_string(): void
    {
        config(['services.anthropic.api_key' => 'sk-ant-test']);

        $expectedMarkdown = "# Introduction\n\nCeci est un paragraphe.";

        Http::fake([
            'api.anthropic.com/*' => Http::response([
                'content' => [
                    ['type' => 'text', 'text' => $expectedMarkdown],
                ],
            ], 200),
        ]);

        $path = $this->createDocx(function (PhpWord $pw) {
            $section = $pw->addSection();
            $section->addText('Introduction');
            $section->addText('Ceci est un paragraphe.');
        });

        $result = $this->service->toMarkdown($path);
        unlink($path);

        $this->assertSame($expectedMarkdown, $result);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.anthropic.com/v1/messages'
                && $request->header('x-api-key')[0] === 'sk-ant-test'
                && $request['model'] === 'claude-haiku-4-5-20251001'
                && str_contains($request['messages'][0]['content'], 'Introduction');
        });
    }

    public function test_throws_when_document_empty(): void
    {
        config(['services.anthropic.api_key' => 'sk-ant-test']);

        $path = $this->createDocx(function (PhpWord $pw) {
            $pw->addSection(); // empty section
        });

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('texte exploitable');

        try {
            $this->service->toMarkdown($path);
        } finally {
            unlink($path);
        }
    }
}
```

- [ ] **Step 1.3: Run (fail)**

```bash
vendor/bin/phpunit tests/Unit/Services/DocumentConversionServiceTest.php
```

Expected: `Class "App\Services\DocumentConversionService" not found`.

- [ ] **Step 1.4: Implement**

File `app/Services/DocumentConversionService.php`:

```php
<?php

namespace App\Services;

use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\Element\ListItem;
use PhpOffice\PhpWord\Element\ListItemRun;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Title;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Http;

class DocumentConversionService
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    private const MODEL = 'claude-haiku-4-5-20251001';
    private const MAX_TOKENS = 8192;
    private const TIMEOUT = 60;

    private const SYSTEM_PROMPT = <<<'PROMPT'
Tu es un convertisseur de documents scientifiques. Convertis le texte suivant en Markdown structuré.
Règles :
- Utilise # / ## / ### pour les titres (déduis le niveau du contexte et de la mise en forme)
- Conserve **gras** et *italique*
- Les noms d'espèces (noms latins binomiaux) doivent être en *italique*
- Utilise des tableaux pipe Markdown pour les tableaux
- Utilise des listes à puces ou numérotées selon le contexte
- Utilise > pour les citations
- Ne modifie pas le contenu textuel, ne résume pas, ne commente pas
- Retourne uniquement le Markdown, sans explication ni balise de code
PROMPT;

    /**
     * Convert a .docx or .odt file to Markdown via Claude API.
     */
    public function toMarkdown(string $filePath): string
    {
        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey)) {
            throw new \RuntimeException(
                'La conversion de documents Word nécessite la configuration de l\'API. Importez un fichier .md à la place.'
            );
        }

        $text = $this->extractText($filePath);

        if (trim($text) === '') {
            throw new \RuntimeException(
                'Le document ne contient pas de texte exploitable.'
            );
        }

        return $this->callClaudeApi($apiKey, $text);
    }

    /**
     * Extract plain text from a .docx or .odt file.
     */
    public function extractText(string $filePath): string
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $readerName = $ext === 'odt' ? 'ODText' : 'Word2007';

        $phpWord = IOFactory::load($filePath, $readerName);
        $lines = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $line = $this->elementToText($element);
                if ($line !== '') {
                    $lines[] = $line;
                }
            }
        }

        return implode("\n\n", $lines);
    }

    private function elementToText(AbstractElement $element): string
    {
        if ($element instanceof Title) {
            $text = $element->getText();
            return is_string($text) ? $text : $this->textRunToPlain($text);
        }

        if ($element instanceof Table) {
            return $this->tableToText($element);
        }

        if ($element instanceof ListItem) {
            return '- ' . ($element->getText() ?? '');
        }

        if ($element instanceof ListItemRun) {
            return '- ' . $this->textRunToPlain($element);
        }

        if ($element instanceof TextRun) {
            return $this->textRunToPlain($element);
        }

        if ($element instanceof Text) {
            return (string) ($element->getText() ?? '');
        }

        return '';
    }

    private function textRunToPlain($textRun): string
    {
        $parts = [];
        foreach ($textRun->getElements() as $child) {
            if ($child instanceof Text) {
                $parts[] = (string) ($child->getText() ?? '');
            }
        }
        return implode('', $parts);
    }

    private function tableToText(Table $table): string
    {
        $rowTexts = [];
        foreach ($table->getRows() as $row) {
            $cells = [];
            foreach ($row->getCells() as $cell) {
                $cellParts = [];
                foreach ($cell->getElements() as $el) {
                    if ($el instanceof Text) {
                        $cellParts[] = (string) ($el->getText() ?? '');
                    } elseif ($el instanceof TextRun) {
                        $cellParts[] = $this->textRunToPlain($el);
                    }
                }
                $cells[] = implode('', $cellParts);
            }
            $rowTexts[] = implode(' | ', $cells);
        }
        return implode("\n", $rowTexts);
    }

    private function callClaudeApi(string $apiKey, string $text): string
    {
        $response = Http::timeout(self::TIMEOUT)
            ->withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
            ->post(self::API_URL, [
                'model' => self::MODEL,
                'max_tokens' => self::MAX_TOKENS,
                'system' => self::SYSTEM_PROMPT,
                'messages' => [
                    ['role' => 'user', 'content' => $text],
                ],
            ]);

        if (!$response->successful()) {
            $error = $response->json('error.message', 'Erreur inconnue');
            throw new \RuntimeException(
                "La conversion a échoué : {$error}. Réessayez ou importez un fichier .md."
            );
        }

        $content = $response->json('content.0.text', '');

        if (empty($content)) {
            throw new \RuntimeException(
                'La conversion a retourné un résultat vide. Réessayez ou importez un fichier .md.'
            );
        }

        return $content;
    }
}
```

- [ ] **Step 1.5: Run (pass)**

```bash
vendor/bin/phpunit tests/Unit/Services/DocumentConversionServiceTest.php
```

Expected: `OK (8 tests, ...)`.

- [ ] **Step 1.6: Commit**

```bash
git add config/services.php app/Services/DocumentConversionService.php tests/Unit/Services/DocumentConversionServiceTest.php
git commit -m "feat(journal): DocumentConversionService with Claude API for Word/ODT to Markdown conversion"
```

---

## Task 2: Controller update + cleanup

**Files:**
- Modify: `app/Http/Controllers/Admin/SubmissionController.php`
- Delete: `app/Services/DocumentToBlocksService.php`
- Delete: `tests/Unit/Services/DocumentToBlocksServiceTest.php`

---

- [ ] **Step 2.1: Update controller imports and method**

In `app/Http/Controllers/Admin/SubmissionController.php`:

Replace the import line:

```php
use App\Services\DocumentToBlocksService;
```

With:

```php
use App\Services\DocumentConversionService;
```

Then replace the `importMarkdown` method (line ~758-790) with:

```php
    /**
     * Import a document file and convert it to content blocks
     */
    public function importMarkdown(Request $request, Submission $submission)
    {
        $request->validate([
            'markdown_file' => 'required|file|max:5120',
        ]);

        $file = $request->file('markdown_file');
        $ext = strtolower($file->getClientOriginalExtension());

        $markdownExts = ['md', 'txt', 'markdown'];
        $documentExts = ['docx', 'odt'];

        if (!in_array($ext, [...$markdownExts, ...$documentExts], true)) {
            return response()->json([
                'error' => 'Format non supporté. Utilisez un fichier .md, .txt, .docx ou .odt.',
            ], 422);
        }

        try {
            if (in_array($ext, $markdownExts, true)) {
                $markdown = file_get_contents($file->getRealPath());
            } else {
                $markdown = app(DocumentConversionService::class)->toMarkdown($file->getRealPath());
            }

            $blocks = app(MarkdownToBlocksService::class)->parse($markdown);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'blocks' => $blocks,
            'count' => count($blocks),
        ]);
    }
```

- [ ] **Step 2.2: Delete old service and tests**

```bash
git rm app/Services/DocumentToBlocksService.php
git rm tests/Unit/Services/DocumentToBlocksServiceTest.php
```

- [ ] **Step 2.3: Run full suite**

```bash
vendor/bin/phpunit
```

Expected: all green. The 10 old `DocumentToBlocksServiceTest` tests are gone, the 8 new `DocumentConversionServiceTest` tests pass.

- [ ] **Step 2.4: Commit**

```bash
git add app/Http/Controllers/Admin/SubmissionController.php
git commit -m "feat(admin): use Claude API for Word/ODT conversion, remove old DocumentToBlocksService"
```

---

## Task 3: Doc + full suite

**Files:**
- Modify: `resources/views/admin/documentation/index.blade.php`

---

- [ ] **Step 3.1: Update documentation**

In `resources/views/admin/documentation/index.blade.php`, find the "Import de documents" section (line ~1808). Replace:

```blade
                <h3>Import de documents</h3>
                <p>Dans la page de maquettage d'un article (<code>/extranet/submissions/{id}/layout</code>), un bouton <strong>"Importer un document"</strong> (violet) permet d'uploader un fichier qui est automatiquement converti en blocs de maquette.</p>

                <h4>Formats acceptés</h4>
                <ul>
                    <li><strong>Markdown</strong> (<code>.md</code>, <code>.txt</code>, <code>.markdown</code>) — conversion directe via CommonMark</li>
                    <li><strong>Word</strong> (<code>.docx</code>) — parsing natif via PhpWord, pas de dépendance externe</li>
                    <li><strong>OpenDocument</strong> (<code>.odt</code>) — même moteur que Word</li>
                </ul>
                <p>Taille maximale : 5 Mo.</p>

                <h4>Types de blocs générés</h4>
                <ul>
                    <li>Titres → bloc Titre (H1 / H2 / H3). Pour les fichiers Word/ODT : détection par style Word (<code>Heading1</code>, etc.) ou par heuristique (texte court + gras + grande police).</li>
                    <li>Paragraphes → bloc Paragraphe (gras, italique, exposant, indice conservés en HTML inline)</li>
                    <li>Tableaux → bloc Tableau (1re ligne = en-têtes)</li>
                    <li>Listes à puces / numérotées → bloc Liste</li>
                    <li>Images Markdown <code>![alt](url)</code> → bloc Image (Word/ODT : images ignorées, envoyées séparément)</li>
                    <li>Citations Markdown <code>&gt; texte</code> → bloc Citation (pas d'équivalent en Word)</li>
                </ul>

                <div class="doc-info">
                    <strong>Attention :</strong> l'import remplace tous les blocs existants (avec confirmation). Les images sont envoyées séparément en haute résolution après acceptation — elles ne sont pas extraites des fichiers Word.<br>
                    <strong>Limites Word/ODT :</strong> les titres formatés sans gras ni changement de taille sont traités comme des paragraphes. Les listes imbriquées sont aplaties. Les fichiers <code>.doc</code> (ancien format) ne sont pas supportés.
                </div>
```

With:

```blade
                <h3>Import de documents</h3>
                <p>Dans la page de maquettage d'un article (<code>/extranet/submissions/{id}/layout</code>), un bouton <strong>"Importer un document"</strong> (violet) permet d'uploader un fichier qui est automatiquement converti en blocs de maquette.</p>

                <h4>Formats acceptés</h4>
                <ul>
                    <li><strong>Markdown</strong> (<code>.md</code>, <code>.txt</code>, <code>.markdown</code>) — conversion directe via CommonMark. Idéal si le maquettiste est à l'aise avec le format Markdown.</li>
                    <li><strong>Word</strong> (<code>.docx</code>) — le texte est extrait puis converti en Markdown structuré via l'API Claude (IA). Les titres, listes, tableaux, noms d'espèces en italique sont automatiquement reconnus.</li>
                    <li><strong>OpenDocument</strong> (<code>.odt</code>) — même processus que Word.</li>
                </ul>
                <p>Taille maximale : 5 Mo.</p>

                <h4>Conversion intelligente (Word/ODT)</h4>
                <p>Les fichiers Word et ODT sont convertis via l'API Claude (modèle Haiku). L'IA analyse la structure du document et produit un Markdown propre avec :</p>
                <ul>
                    <li>Titres correctement hiérarchisés (H1 / H2 / H3)</li>
                    <li>Formatage inline conservé (gras, italique, exposant, indice)</li>
                    <li>Noms d'espèces automatiquement mis en italique</li>
                    <li>Tableaux convertis en format Markdown</li>
                    <li>Listes à puces et numérotées</li>
                </ul>

                <div class="doc-info">
                    <strong>Attention :</strong> l'import remplace tous les blocs existants (avec confirmation). Les images ne sont pas extraites des fichiers Word — elles sont envoyées séparément en haute résolution après acceptation.<br>
                    <strong>Prérequis Word/ODT :</strong> la conversion nécessite que la clé API Anthropic soit configurée (<code>ANTHROPIC_API_KEY</code> dans <code>.env</code>). Si elle n'est pas configurée, un message invite à importer un fichier <code>.md</code> à la place.
                </div>
```

- [ ] **Step 3.2: Full suite**

```bash
php artisan view:clear
vendor/bin/phpunit
```

Expected: all green (152 + 8 = 160 tests).

- [ ] **Step 3.3: Commit**

```bash
git add resources/views/admin/documentation/index.blade.php
git commit -m "docs(extranet): document Claude API conversion for Word/ODT import"
```

---

## Récapitulatif des commits

1. `feat(journal): DocumentConversionService with Claude API for Word/ODT to Markdown conversion`
2. `feat(admin): use Claude API for Word/ODT conversion, remove old DocumentToBlocksService`
3. `docs(extranet): document Claude API conversion for Word/ODT import`

---

## Notes de vigilance

- **Clé API** : doit être ajoutée dans `.env` : `ANTHROPIC_API_KEY=sk-ant-...`. Sans elle, seul l'import `.md` fonctionne.
- **Modèle** : `claude-haiku-4-5-20251001` — le plus rapide et le moins cher. Suffisant pour la conversion structurelle.
- **Timeout** : 60 secondes — les articles longs peuvent prendre du temps.
- **Max tokens** : 8192 — suffisant pour un article scientifique standard. Si un article dépasse, le Markdown sera tronqué (le maquettiste peut corriger dans l'éditeur de blocs).
- **phpoffice/phpword** reste installé — utilisé uniquement pour l'extraction de texte brut (plus fiable que de lire le XML directement).
- **Pas de SDK Anthropic** — un simple `Http::post()` suffit. Évite une dépendance supplémentaire.
- **Pas de Claude dans les commits**.
