# Chersotis — Conversion Word côté client + enrichissement unifié — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Déplacer la conversion docx→md côté navigateur via `word-to-markdown`, puis unifier le flux pour que tous les imports (.md direct et .docx converti) passent par Claude Haiku pour l'enrichissement (extraction métadonnées, taxons, formatage Harvard).

**Architecture :** Le JS du block editor charge `mammoth.js` (lib JS de conversion docx→HTML→texte, utilisée par word-to-markdown) pour convertir le .docx en Markdown côté client, puis envoie le Markdown au serveur. Le controller reçoit toujours du Markdown (champ `markdown_content` en POST), le passe à `DocumentConversionService::enrichMarkdown()` pour l'enrichissement Claude, puis à `MarkdownToBlocksService` pour les blocs. Le phpword extraction côté serveur est supprimé.

**Tech Stack :** Laravel 12, mammoth.js (CDN), API Anthropic Claude Haiku, Alpine.js.

**Spec :** Flux unifié validé en conversation.

---

## File Structure

### Fichiers modifiés

| Chemin | Changement |
|---|---|
| `app/Services/DocumentConversionService.php` | Supprimer `toMarkdown()`, `extractText()` et helpers phpword. Renommer `toStructured()` en `enrichMarkdown(string $markdown): array`. Le service ne reçoit plus un fichier mais du Markdown. |
| `app/Http/Controllers/Admin/SubmissionController.php` | Le controller reçoit `markdown_content` (texte) au lieu d'un fichier pour l'enrichissement. Garde le file upload pour le .md direct. Unifie le flux. |
| `resources/views/admin/submissions/_block-editor.blade.php` | Ajouter mammoth.js, convertir .docx→md côté client, envoyer le Markdown au serveur |
| `resources/views/admin/documentation/index.blade.php` | Doc mise à jour |
| `tests/Unit/Services/DocumentConversionServiceTest.php` | Adapter : supprimer tests phpword, tester `enrichMarkdown()` |

### Dépendances supprimées

`phpoffice/phpword` n'est plus nécessaire pour l'import (mais vérifier s'il est utilisé ailleurs avant de le supprimer).

---

## Task 1: DocumentConversionService — enrichMarkdown() (TDD)

**Files:**
- Modify: `app/Services/DocumentConversionService.php`
- Modify: `tests/Unit/Services/DocumentConversionServiceTest.php`

---

- [ ] **Step 1.1: Rewrite tests**

Replace the content of `tests/Unit/Services/DocumentConversionServiceTest.php` with:

```php
<?php

namespace Tests\Unit\Services;

use App\Services\DocumentConversionService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DocumentConversionServiceTest extends TestCase
{
    private DocumentConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DocumentConversionService();
    }

    public function test_enrich_markdown_returns_structured_array(): void
    {
        $jsonResponse = json_encode([
            'title' => 'Mon article scientifique',
            'markdown' => "## Résumé\n\nContenu de l'article.",
            'references' => ['Dupont J. (2023). Titre. *Revue*, 6(1), 17–24.'],
            'authors_affiliations' => ['Jean DUPONT : Université Paris, jean@univ.fr'],
            'acknowledgements' => 'Merci au CNRS.',
            'taxons' => ['Chazara briseis', 'Melanargia galathea'],
        ]);

        Http::fake([
            'https://api.anthropic.com/v1/messages' => Http::response([
                'content' => [['type' => 'text', 'text' => $jsonResponse]],
            ], 200),
        ]);

        config(['services.anthropic.api_key' => 'test-key']);

        $result = $this->service->enrichMarkdown('# Titre\n\nContenu');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('markdown', $result);
        $this->assertArrayHasKey('references', $result);
        $this->assertArrayHasKey('authors_affiliations', $result);
        $this->assertArrayHasKey('acknowledgements', $result);
        $this->assertArrayHasKey('taxons', $result);
        $this->assertSame('Mon article scientifique', $result['title']);
        $this->assertCount(1, $result['references']);
        $this->assertCount(2, $result['taxons']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.anthropic.com/v1/messages'
                && str_contains($request->body(), 'Titre')
                && str_contains($request->body(), 'claude-haiku');
        });
    }

    public function test_enrich_markdown_fallback_on_non_json(): void
    {
        Http::fake([
            'https://api.anthropic.com/v1/messages' => Http::response([
                'content' => [['type' => 'text', 'text' => '# Just markdown']],
            ], 200),
        ]);

        config(['services.anthropic.api_key' => 'test-key']);

        $result = $this->service->enrichMarkdown('# Just markdown');

        $this->assertSame('# Just markdown', $result['markdown']);
        $this->assertSame([], $result['references']);
        $this->assertSame('', $result['title']);
    }

    public function test_throws_when_api_key_missing(): void
    {
        config(['services.anthropic.api_key' => null]);

        $this->expectException(\RuntimeException::class);
        $this->service->enrichMarkdown('# Test');
    }

    public function test_throws_when_api_fails(): void
    {
        Http::fake([
            'https://api.anthropic.com/v1/messages' => Http::response(
                ['error' => ['message' => 'Unauthorized']], 401
            ),
        ]);

        config(['services.anthropic.api_key' => 'test-key']);

        $this->expectException(\RuntimeException::class);
        $this->service->enrichMarkdown('# Test');
    }

    public function test_throws_when_markdown_empty(): void
    {
        config(['services.anthropic.api_key' => 'test-key']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/texte exploitable/i');
        $this->service->enrichMarkdown('   ');
    }
}
```

- [ ] **Step 1.2: Run (fail)**

```bash
vendor/bin/phpunit tests/Unit/Services/DocumentConversionServiceTest.php
```

Expected: `Call to undefined method enrichMarkdown()`.

- [ ] **Step 1.3: Rewrite DocumentConversionService**

Replace the entire content of `app/Services/DocumentConversionService.php` with:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Enriches Markdown content via Claude Haiku API.
 *
 * Extracts structured metadata (title, references, affiliations,
 * acknowledgements, taxons) and returns clean Markdown body.
 */
class DocumentConversionService
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    private const MODEL = 'claude-haiku-4-5-20251001';
    private const MAX_TOKENS = 16384;
    private const TIMEOUT = 180;

    private const SYSTEM_PROMPT = <<<'PROMPT'
Tu es un convertisseur de documents scientifiques entomologiques.
Analyse le Markdown suivant et retourne un JSON structuré avec ces champs :

- "title" : le titre principal de l'article (sans les auteurs)
- "markdown" : le corps de l'article en Markdown structuré (## pour les sous-titres, **gras**, *italique*, tableaux pipe, listes). NE PAS inclure : le titre principal, les affiliations des auteurs, les références bibliographiques, les remerciements.
- "references" : tableau JSON de strings, une référence par entrée, dans l'ordre d'apparition. Chaque référence doit être formatée en style Harvard : Auteur(s) (année). Titre. *Revue*, volume(numéro), pages. Si la référence est un livre : Auteur(s) (année). *Titre*. Éditeur, Lieu, pages. Reformater si nécessaire.
- "authors_affiliations" : tableau JSON de strings, un auteur par entrée, au format "Prénom NOM : affiliation complète, email"
- "acknowledgements" : texte des remerciements (chaîne vide si absent)
- "taxons" : tableau JSON des noms d'espèces (noms latins binomiaux) trouvés dans le texte, sans doublons

Règles pour le Markdown :
- Les noms d'espèces doivent être en *italique*
- Conserver le formatage (gras, italique, exposant, indice)
- Utiliser des tableaux pipe pour les tableaux
- Ne pas modifier le contenu textuel, ne pas résumer
- Retourner uniquement le JSON, sans explication ni balise de code
PROMPT;

    /**
     * Enrich Markdown content via Claude API.
     *
     * @return array{title: string, markdown: string, references: string[], authors_affiliations: string[], acknowledgements: string, taxons: string[]}
     * @throws \RuntimeException
     */
    public function enrichMarkdown(string $markdown): array
    {
        if (trim($markdown) === '') {
            throw new \RuntimeException(
                'Le document ne contient pas de texte exploitable.'
            );
        }

        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey)) {
            throw new \RuntimeException(
                'La clé API Anthropic n\'est pas configurée.'
            );
        }

        \Log::info('DocumentConversion: enriching markdown', [
            'chars' => strlen($markdown),
            'words' => str_word_count($markdown),
        ]);

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])
        ->timeout(self::TIMEOUT)
        ->post(self::API_URL, [
            'model'      => self::MODEL,
            'max_tokens' => self::MAX_TOKENS,
            'system'     => self::SYSTEM_PROMPT,
            'messages'   => [
                ['role' => 'user', 'content' => $markdown],
            ],
        ]);

        if ($response->failed()) {
            $errorMessage = $response->json('error.message') ?? $response->body();
            throw new \RuntimeException(
                'Erreur de l\'API Anthropic : ' . $errorMessage
            );
        }

        $content = $response->json('content.0.text', '');

        if (empty($content)) {
            throw new \RuntimeException(
                'L\'API Anthropic n\'a retourné aucun contenu.'
            );
        }

        return $this->parseStructuredResponse($content);
    }

    /**
     * Parse Claude response as JSON. Fallback to plain markdown if parsing fails.
     */
    private function parseStructuredResponse(string $content): array
    {
        $fallback = [
            'title'                => '',
            'markdown'             => $content,
            'references'           => [],
            'authors_affiliations' => [],
            'acknowledgements'     => '',
            'taxons'               => [],
        ];

        // Strip markdown code fences if present
        $stripped = preg_replace('/^```(?:json)?\s*/i', '', trim($content));
        $stripped = preg_replace('/\s*```$/', '', $stripped ?? $content);

        $decoded = json_decode($stripped ?? $content, true);

        if (!is_array($decoded)) {
            return $fallback;
        }

        return [
            'title'                => $decoded['title'] ?? '',
            'markdown'             => $decoded['markdown'] ?? '',
            'references'           => $this->flattenToStrings($decoded['references'] ?? []),
            'authors_affiliations' => $this->flattenToStrings($decoded['authors_affiliations'] ?? []),
            'acknowledgements'     => $decoded['acknowledgements'] ?? '',
            'taxons'               => $this->flattenToStrings($decoded['taxons'] ?? []),
        ];
    }

    /**
     * Flatten an array of mixed items (strings or nested arrays) into strings.
     */
    private function flattenToStrings(array $items): array
    {
        return array_map(function ($item) {
            if (is_string($item)) {
                return $item;
            }
            if (is_array($item)) {
                return $this->flattenValue($item);
            }
            return (string) $item;
        }, $items);
    }

    private function flattenValue(array $item): string
    {
        $parts = [];
        foreach ($item as $value) {
            if (is_array($value)) {
                $parts[] = $this->flattenValue($value);
            } elseif (is_string($value) || is_numeric($value)) {
                $parts[] = (string) $value;
            }
        }
        return implode(', ', array_filter($parts));
    }
}
```

- [ ] **Step 1.4: Run (pass)**

```bash
vendor/bin/phpunit tests/Unit/Services/DocumentConversionServiceTest.php
```

Expected: `OK (5 tests, ...)`.

- [ ] **Step 1.5: Commit**

```bash
git add app/Services/DocumentConversionService.php tests/Unit/Services/DocumentConversionServiceTest.php
git commit -m "refactor(journal): DocumentConversionService.enrichMarkdown() replaces file-based conversion"
```

---

## Task 2: Controller — flux unifié Markdown → enrichissement

**Files:**
- Modify: `app/Http/Controllers/Admin/SubmissionController.php`

---

- [ ] **Step 2.1: Update importMarkdown method**

Replace the `importMarkdown` method in `app/Http/Controllers/Admin/SubmissionController.php` with:

```php
    /**
     * Import a document and convert it to enriched content blocks.
     *
     * Accepts either:
     * - A file upload (.md, .txt, .markdown, .docx, .odt) — for .docx/.odt the JS converts to MD client-side
     * - A JSON body with 'markdown_content' — sent by JS after client-side docx→md conversion
     */
    public function importMarkdown(Request $request, Submission $submission)
    {
        set_time_limit(300);

        // Accept either file upload or markdown content from client-side conversion
        if ($request->has('markdown_content')) {
            $markdown = $request->input('markdown_content');
        } else {
            $request->validate([
                'markdown_file' => 'required|file|max:5120',
            ]);

            $file = $request->file('markdown_file');
            $ext = strtolower($file->getClientOriginalExtension());

            if (!in_array($ext, ['md', 'txt', 'markdown'], true)) {
                return response()->json([
                    'error' => 'Format non supporté. Utilisez un fichier .md, .txt, .docx ou .odt.',
                ], 422);
            }

            $markdown = file_get_contents($file->getRealPath());
        }

        if (empty(trim($markdown))) {
            return response()->json(['error' => 'Le document est vide.'], 422);
        }

        try {
            $structured = app(DocumentConversionService::class)->enrichMarkdown($markdown);
            $blocks = app(MarkdownToBlocksService::class)->parse($structured['markdown']);
            $blocks = $this->enrichBlocksWithTaxonLinks($blocks, $structured['taxons']);

            $refs = is_array($structured['references']) ? $structured['references'] : [];
            $affils = is_array($structured['authors_affiliations']) ? $structured['authors_affiliations'] : [];

            return response()->json([
                'blocks' => $blocks,
                'count' => count($blocks),
                'references' => implode("\n", array_map('strval', $refs)),
                'authors_affiliations' => implode("\n", array_map('strval', $affils)),
                'acknowledgements' => (string) ($structured['acknowledgements'] ?? ''),
                'detected_title' => (string) ($structured['title'] ?? ''),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
```

Also remove the `use App\Services\DocumentToBlocksService;` import if still present (it was already removed, but double-check).

- [ ] **Step 2.2: Verify**

```bash
vendor/bin/phpunit
```

Expected: all green.

- [ ] **Step 2.3: Commit**

```bash
git add app/Http/Controllers/Admin/SubmissionController.php
git commit -m "feat(admin): unified import flow — all content enriched via Claude API"
```

---

## Task 3: Block editor JS — mammoth.js + client-side conversion

**Files:**
- Modify: `resources/views/admin/submissions/_block-editor.blade.php`

---

- [ ] **Step 3.1: Add mammoth.js CDN**

At the very end of the `_block-editor.blade.php` file (after the closing `</script>` tag), add:

```blade
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.8.0/mammoth.browser.min.js"></script>
```

- [ ] **Step 3.2: Rewrite importMarkdown JS function**

Find the `async importMarkdown(event)` function. Replace it entirely (from `async importMarkdown(event) {` to its closing `event.target.value = '';` and the `},` after) with:

```javascript
        async importMarkdown(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (this.blocks.length > 0) {
                if (!confirm('Cela va remplacer les ' + this.blocks.length + ' blocs existants et les champs sidebar. Continuer ?')) {
                    event.target.value = '';
                    return;
                }
            }

            const ext = file.name.split('.').pop().toLowerCase();
            const isWord = ['docx', 'odt'].includes(ext);

            this.importing = true;

            try {
                let markdown;

                if (isWord) {
                    // Client-side conversion via mammoth.js
                    if (typeof mammoth === 'undefined') {
                        alert('La bibliothèque de conversion Word n\'est pas chargée. Réessayez.');
                        this.importing = false;
                        event.target.value = '';
                        return;
                    }
                    const arrayBuffer = await file.arrayBuffer();
                    const result = await mammoth.convertToMarkdown(arrayBuffer);
                    markdown = result.value;
                } else {
                    // .md/.txt: read file content directly
                    markdown = await file.text();
                }

                // Send markdown to server for Claude enrichment
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                    || document.querySelector('input[name="_token"]')?.value
                    || '{{ csrf_token() }}';

                const response = await fetch('{{ route("admin.submissions.import-markdown", $submission->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ markdown_content: markdown }),
                });

                const data = await response.json();

                if (!response.ok) {
                    alert(data.error || 'Erreur lors de l\'import.');
                    return;
                }

                this.blocks = data.blocks;
                this.blockIdCounter = data.blocks.length + 1;

                // Pre-fill sidebar fields
                if (data.references !== undefined) {
                    const refsEl = document.getElementById('sidebar-references');
                    if (refsEl) refsEl.value = data.references;

                    const affilEl = document.getElementById('sidebar-affiliations');
                    if (affilEl) affilEl.value = data.authors_affiliations;

                    const ackEl = document.getElementById('sidebar-acknowledgements');
                    if (ackEl) ackEl.value = data.acknowledgements;
                }

                // Show detected title banner if different from current title
                if (data.detected_title) {
                    const currentTitle = @js($submission->title);
                    const detected = data.detected_title.trim();
                    if (detected && detected !== currentTitle) {
                        const banner = document.getElementById('detected-title-banner');
                        const titleText = document.getElementById('detected-title-text');
                        const updateBtn = document.getElementById('update-title-btn');
                        if (banner && titleText) {
                            titleText.textContent = detected;
                            banner.style.display = 'flex';

                            if (updateBtn) {
                                updateBtn.onclick = async () => {
                                    const resp = await fetch('{{ route("admin.submissions.update-title", $submission->id) }}', {
                                        method: 'PATCH',
                                        headers: {
                                            'X-CSRF-TOKEN': csrfToken,
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                        },
                                        body: JSON.stringify({ title: detected }),
                                    });
                                    if (resp.ok) {
                                        banner.style.display = 'none';
                                        document.querySelector('.layout-title').textContent = detected;
                                    }
                                };
                            }
                        }
                    }
                }

                const extra = data.references !== undefined
                    ? ' + références, affiliations et remerciements pré-remplis'
                    : '';
                alert('Import réussi : ' + data.count + ' blocs créés' + extra + '.');
            } catch (error) {
                alert('Erreur : ' + error.message);
            }

            this.importing = false;
            event.target.value = '';
        },
```

- [ ] **Step 3.3: Remove the old .docx confirm dialog**

The old confirm dialog for Word files (`if (isWord && !confirm('La conversion Word/ODT via IA peut prendre 30 à 60 secondes...'))`) is no longer needed — the client-side conversion is instant, and the enrichissement takes the same time for all formats. This is already handled by the rewrite above.

- [ ] **Step 3.4: Verify**

```bash
php artisan view:clear
vendor/bin/phpunit
```

Expected: all green.

- [ ] **Step 3.5: Commit**

```bash
git add resources/views/admin/submissions/_block-editor.blade.php
git commit -m "feat(admin): client-side docx→md via mammoth.js + unified Claude enrichment for all imports"
```

---

## Task 4: Cleanup + doc

**Files:**
- Modify: `resources/views/admin/documentation/index.blade.php`

---

- [ ] **Step 4.1: Update documentation**

In `resources/views/admin/documentation/index.blade.php`, find the "Import de documents" section. Replace from `<h3>Import de documents</h3>` through the closing `</div>` of the "doc-info" block (before `<h4>Sauvegarde et aperçu PDF</h4>`) with:

```blade
                <h3>Import de documents</h3>
                <p>Dans la page de maquettage d'un article (<code>/extranet/submissions/{id}/layout</code>), un bouton <strong>"Importer un document"</strong> (violet) permet d'uploader un fichier qui est automatiquement converti en blocs de maquette enrichis.</p>

                <h4>Formats acceptés</h4>
                <ul>
                    <li><strong>Word</strong> (<code>.docx</code>) — converti en Markdown côté navigateur (instantané), puis enrichi par l'IA</li>
                    <li><strong>Markdown</strong> (<code>.md</code>, <code>.txt</code>, <code>.markdown</code>) — enrichi directement par l'IA</li>
                </ul>
                <p>Taille maximale : 5 Mo. Tous les formats passent par le même enrichissement IA.</p>

                <h4>Enrichissement intelligent (Claude Haiku)</h4>
                <p>L'IA analyse le contenu du document et extrait automatiquement :</p>
                <ul>
                    <li><strong>Corps de l'article</strong> — titres hiérarchisés, formatage, tableaux, listes. Le titre, les affiliations, les références et les remerciements sont <strong>retirés du corps</strong> et placés dans les champs dédiés.</li>
                    <li><strong>Références bibliographiques</strong> — extraites, reformatées en style Harvard, et pré-remplies dans le champ sidebar</li>
                    <li><strong>Affiliations auteurs</strong> — extraites et pré-remplies dans le champ sidebar</li>
                    <li><strong>Remerciements</strong> — extraits et pré-remplis dans le champ sidebar</li>
                    <li><strong>Noms de taxons</strong> — détectés et enrichis avec un lien vers <a href="https://oreina.org/artemisiae/" target="_blank">Artemisiae</a></li>
                    <li><strong>Titre détecté</strong> — si différent du titre de la soumission, un bandeau propose de le mettre à jour</li>
                </ul>

                <div class="doc-info">
                    <strong>Attention :</strong> l'import remplace tous les blocs existants et les champs sidebar (avec confirmation).<br>
                    <strong>Prérequis :</strong> la clé API Anthropic doit être configurée (<code>ANTHROPIC_PLATFORM_KEY</code> dans <code>.env</code>). L'enrichissement prend 30 à 120 secondes selon la taille du document.
                </div>
```

- [ ] **Step 4.2: Check if phpword can be removed**

```bash
grep -r "PhpWord\|phpword\|PhpOffice" app/ --include="*.php" -l
```

If only `DocumentConversionService.php` used it (and it's now removed), run:

```bash
composer remove phpoffice/phpword
```

If other files still use it, keep it.

- [ ] **Step 4.3: Full suite**

```bash
php artisan view:clear
vendor/bin/phpunit
```

Expected: all green.

- [ ] **Step 4.4: Commit**

```bash
git add resources/views/admin/documentation/index.blade.php composer.json composer.lock
git commit -m "docs(extranet): document unified import with client-side Word conversion + Claude enrichment"
```

---

## Récapitulatif des commits

1. `refactor(journal): DocumentConversionService.enrichMarkdown() replaces file-based conversion`
2. `feat(admin): unified import flow — all content enriched via Claude API`
3. `feat(admin): client-side docx→md via mammoth.js + unified Claude enrichment for all imports`
4. `docs(extranet): document unified import with client-side Word conversion + Claude enrichment`

---

## Notes de vigilance

- **mammoth.js** : version 1.8.0 via CDN. Supporte `.docx` nativement. Ne supporte **pas** `.odt` — si `.odt` est nécessaire, garder phpword en fallback côté serveur pour ce format uniquement. Le `accept` de l'input file devrait passer à `.md,.txt,.markdown,.docx` (sans `.odt`).
- **Content-Type** : le JS envoie maintenant du `application/json` avec `markdown_content` au lieu d'un `multipart/form-data` avec un fichier. Le controller accepte les deux formats (JSON body ou file upload).
- **CSRF** : le token CSRF est envoyé dans le header `X-CSRF-TOKEN`, pas dans le body. Le middleware Laravel le vérifie dans les deux cas.
- **Taille max** : la validation `max:5120` (5 Mo) s'applique au file upload. Pour le JSON body, le Markdown converti est toujours plus petit que le .docx d'origine.
- **Pas de Claude dans les commits**.
