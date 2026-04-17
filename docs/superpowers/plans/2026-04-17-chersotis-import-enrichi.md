# Chersotis — Import enrichi via Claude API — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Enrichir l'import Word/ODT pour que Claude retourne un JSON structuré (corps sans métadonnées, références, affiliations, remerciements, taxons, titre), que le controller enrichisse les blocs avec des liens Artemisiae sur les taxons, et que le JS pré-remplisse la sidebar et propose la mise à jour du titre.

**Architecture :** Le `DocumentConversionService` passe d'un prompt Markdown brut à un prompt JSON structuré avec une nouvelle méthode `toStructured()`. Le controller utilise cette méthode pour les .docx/.odt, enrichit les blocs paragraphes avec des liens Artemisiae, et retourne une réponse JSON élargie. Le JS pré-remplit les textareas sidebar et affiche un bandeau titre.

**Tech Stack :** Laravel 12, PHPUnit 11, API Anthropic (Claude Haiku), Alpine.js.

**Spec :** `docs/superpowers/specs/2026-04-17-chersotis-import-enrichi-design.md`

---

## File Structure

### Fichiers modifiés

| Chemin | Changement |
|---|---|
| `app/Services/DocumentConversionService.php` | Nouveau prompt JSON, nouvelle méthode `toStructured()` |
| `app/Http/Controllers/Admin/SubmissionController.php` | Enrichissement taxons, réponse élargie, route PATCH titre |
| `resources/views/admin/submissions/_block-editor.blade.php` | JS : remplir sidebar + bandeau titre |
| `resources/views/admin/submissions/layout.blade.php` | IDs sur textareas + bandeau titre HTML |
| `routes/admin.php` | Route PATCH titre |
| `tests/Unit/Services/DocumentConversionServiceTest.php` | Tests pour `toStructured()` |
| `resources/views/admin/documentation/index.blade.php` | Doc mise à jour |

---

## Task 1: DocumentConversionService — méthode `toStructured()` (TDD)

**Files:**
- Modify: `app/Services/DocumentConversionService.php`
- Modify: `tests/Unit/Services/DocumentConversionServiceTest.php`

---

- [ ] **Step 1.1: Write tests for toStructured()**

Add these tests at the end of `tests/Unit/Services/DocumentConversionServiceTest.php`:

```php
    public function test_to_structured_returns_array_with_all_keys(): void
    {
        $jsonResponse = json_encode([
            'title' => 'Mon article scientifique',
            'markdown' => '## Résumé\n\nContenu de l\'article.',
            'references' => ['Dupont J., 2023. Titre...', 'Smith A., 2022. Autre...'],
            'authors_affiliations' => ['Jean Dupont : Université Paris'],
            'acknowledgements' => 'Merci au CNRS.',
            'taxons' => ['Chazara briseis', 'Melanargia galathea'],
        ]);

        Http::fake([
            'https://api.anthropic.com/v1/messages' => Http::response([
                'content' => [['type' => 'text', 'text' => $jsonResponse]],
            ], 200),
        ]);

        $phpWord = new PhpWord();
        $phpWord->addSection()->addText('Contenu test');
        $path = $this->createDocx($phpWord);

        try {
            config(['services.anthropic.api_key' => 'test-key']);
            $result = $this->service->toStructured($path);

            $this->assertIsArray($result);
            $this->assertArrayHasKey('title', $result);
            $this->assertArrayHasKey('markdown', $result);
            $this->assertArrayHasKey('references', $result);
            $this->assertArrayHasKey('authors_affiliations', $result);
            $this->assertArrayHasKey('acknowledgements', $result);
            $this->assertArrayHasKey('taxons', $result);
            $this->assertSame('Mon article scientifique', $result['title']);
            $this->assertCount(2, $result['references']);
            $this->assertCount(2, $result['taxons']);
        } finally {
            unlink($path);
        }
    }

    public function test_to_structured_fallback_on_non_json_response(): void
    {
        Http::fake([
            'https://api.anthropic.com/v1/messages' => Http::response([
                'content' => [['type' => 'text', 'text' => '# Titre\n\nJuste du markdown brut.']],
            ], 200),
        ]);

        $phpWord = new PhpWord();
        $phpWord->addSection()->addText('Contenu test');
        $path = $this->createDocx($phpWord);

        try {
            config(['services.anthropic.api_key' => 'test-key']);
            $result = $this->service->toStructured($path);

            $this->assertArrayHasKey('markdown', $result);
            $this->assertStringContainsString('Titre', $result['markdown']);
            $this->assertSame([], $result['references']);
            $this->assertSame([], $result['authors_affiliations']);
            $this->assertSame('', $result['acknowledgements']);
            $this->assertSame('', $result['title']);
            $this->assertSame([], $result['taxons']);
        } finally {
            unlink($path);
        }
    }
```

- [ ] **Step 1.2: Run (fail)**

```bash
vendor/bin/phpunit tests/Unit/Services/DocumentConversionServiceTest.php
```

Expected: `Call to undefined method toStructured()`.

- [ ] **Step 1.3: Implement toStructured()**

In `app/Services/DocumentConversionService.php`:

1. Add a new constant `STRUCTURED_SYSTEM_PROMPT` after the existing `SYSTEM_PROMPT`:

```php
    private const STRUCTURED_SYSTEM_PROMPT = <<<'PROMPT'
Tu es un convertisseur de documents scientifiques entomologiques.
Analyse le texte suivant et retourne un JSON structuré avec ces champs :

- "title" : le titre principal de l'article (sans les auteurs)
- "markdown" : le corps de l'article converti en Markdown structuré (## pour les sous-titres, **gras**, *italique*, tableaux pipe, listes). NE PAS inclure : le titre principal, les affiliations des auteurs, les références bibliographiques, les remerciements.
- "references" : tableau JSON des références bibliographiques, une par entrée, dans l'ordre d'apparition
- "authors_affiliations" : tableau JSON des auteurs avec leurs affiliations (un auteur par entrée)
- "acknowledgements" : texte des remerciements (chaîne vide si absent)
- "taxons" : tableau JSON des noms d'espèces (noms latins binomiaux) trouvés dans le texte, sans doublons

Règles pour le Markdown :
- Les noms d'espèces doivent être en *italique*
- Conserver le formatage (gras, italique, exposant, indice)
- Utiliser des tableaux pipe pour les tableaux
- Ne pas modifier le contenu textuel, ne pas résumer
- Retourner uniquement le JSON, sans explication ni balise de code
PROMPT;
```

2. Change `MAX_TOKENS` from `8192` to `16384`.

3. Add the `toStructured()` method after `toMarkdown()`:

```php
    /**
     * Full pipeline: extract text → Claude API (JSON) → structured array.
     *
     * Returns an array with keys: title, markdown, references, authors_affiliations,
     * acknowledgements, taxons. Falls back to plain markdown if JSON parsing fails.
     *
     * @throws \RuntimeException
     */
    public function toStructured(string $filePath): array
    {
        $text = $this->extractText($filePath);

        if (trim($text) === '') {
            throw new \RuntimeException(
                'Le document ne contient pas de texte exploitable.'
            );
        }

        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey)) {
            throw new \RuntimeException(
                'La clé API Anthropic n\'est pas configurée (services.anthropic.api_key).'
            );
        }

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])
        ->timeout(self::TIMEOUT)
        ->post(self::API_URL, [
            'model'      => self::MODEL,
            'max_tokens' => self::MAX_TOKENS,
            'system'     => self::STRUCTURED_SYSTEM_PROMPT,
            'messages'   => [
                ['role' => 'user', 'content' => $text],
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
     * Parse the Claude response as JSON. Fallback to plain markdown if parsing fails.
     */
    private function parseStructuredResponse(string $content): array
    {
        // Strip markdown code fences if Claude wrapped the JSON
        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', trim($content));
        $cleaned = preg_replace('/\s*```$/i', '', $cleaned);

        $data = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            // Fallback: treat the whole response as plain markdown
            return [
                'title' => '',
                'markdown' => $content,
                'references' => [],
                'authors_affiliations' => [],
                'acknowledgements' => '',
                'taxons' => [],
            ];
        }

        return [
            'title' => $data['title'] ?? '',
            'markdown' => $data['markdown'] ?? '',
            'references' => $data['references'] ?? [],
            'authors_affiliations' => $data['authors_affiliations'] ?? [],
            'acknowledgements' => $data['acknowledgements'] ?? '',
            'taxons' => $data['taxons'] ?? [],
        ];
    }
```

- [ ] **Step 1.4: Run (pass)**

```bash
vendor/bin/phpunit tests/Unit/Services/DocumentConversionServiceTest.php
```

Expected: `OK (10 tests, ...)`.

- [ ] **Step 1.5: Commit**

```bash
git add app/Services/DocumentConversionService.php tests/Unit/Services/DocumentConversionServiceTest.php
git commit -m "feat(journal): DocumentConversionService.toStructured() returns JSON with metadata extraction"
```

---

## Task 2: Controller — enrichissement taxons + réponse élargie + route PATCH titre

**Files:**
- Modify: `app/Http/Controllers/Admin/SubmissionController.php`
- Modify: `routes/admin.php`

---

- [ ] **Step 2.1: Add route for title update**

In `routes/admin.php`, near the existing `import-markdown` route (line ~135), add:

```php
    Route::patch('submissions/{submission}/update-title', [SubmissionController::class, 'updateTitle'])
        ->name('submissions.update-title');
```

- [ ] **Step 2.2: Update importMarkdown method**

In `app/Http/Controllers/Admin/SubmissionController.php`, replace the `importMarkdown` method with:

```php
    /**
     * Import a document file and convert it to content blocks
     */
    public function importMarkdown(Request $request, Submission $submission)
    {
        set_time_limit(180);

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
                $blocks = app(MarkdownToBlocksService::class)->parse($markdown);

                return response()->json([
                    'blocks' => $blocks,
                    'count' => count($blocks),
                ]);
            }

            // Word/ODT: structured conversion via Claude API
            $structured = app(DocumentConversionService::class)->toStructured($file->getRealPath());
            $blocks = app(MarkdownToBlocksService::class)->parse($structured['markdown']);

            // Enrich paragraph blocks with Artemisiae links for taxons
            $blocks = $this->enrichBlocksWithTaxonLinks($blocks, $structured['taxons']);

            return response()->json([
                'blocks' => $blocks,
                'count' => count($blocks),
                'references' => implode("\n", $structured['references']),
                'authors_affiliations' => implode("\n", $structured['authors_affiliations']),
                'acknowledgements' => $structured['acknowledgements'],
                'detected_title' => $structured['title'],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Update the submission title (PATCH from layout editor)
     */
    public function updateTitle(Request $request, Submission $submission)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
        ]);

        $submission->update(['title' => $validated['title']]);

        return response()->json(['success' => true]);
    }

    /**
     * Enrich paragraph blocks by wrapping taxon names with Artemisiae links.
     */
    private function enrichBlocksWithTaxonLinks(array $blocks, array $taxons): array
    {
        if (empty($taxons)) {
            return $blocks;
        }

        foreach ($blocks as &$block) {
            if ($block['type'] !== 'paragraph' || empty($block['content'])) {
                continue;
            }

            foreach ($taxons as $taxon) {
                $escaped = preg_quote($taxon, '/');
                $url = 'https://oreina.org/artemisiae/index.php?module=recherche&action=recherche&recherche=' . urlencode($taxon);

                // Replace <em>Taxon name</em> that is NOT already inside an <a> tag
                $block['content'] = preg_replace(
                    '/(?<!href="[^"]*)<em>' . $escaped . '<\/em>(?![^<]*<\/a>)/u',
                    '<a href="' . $url . '" target="_blank" title="Voir sur Artemisiae"><em>' . htmlspecialchars($taxon, ENT_QUOTES) . '</em></a>',
                    $block['content']
                );
            }
        }

        return $blocks;
    }
```

- [ ] **Step 2.3: Verify**

```bash
php artisan route:list --name=update-title
vendor/bin/phpunit
```

Expected: route listed, all tests green.

- [ ] **Step 2.4: Commit**

```bash
git add app/Http/Controllers/Admin/SubmissionController.php routes/admin.php
git commit -m "feat(admin): enriched import response with metadata, taxon links, and title update route"
```

---

## Task 3: Layout view — IDs sur textareas + bandeau titre

**Files:**
- Modify: `resources/views/admin/submissions/layout.blade.php`

---

- [ ] **Step 3.1: Add IDs to sidebar textareas**

In `resources/views/admin/submissions/layout.blade.php`, add `id` attributes to the three sidebar textareas:

Find (line ~312):
```blade
<textarea name="author_affiliations" class="sidebar-textarea" rows="4"
```
Replace with:
```blade
<textarea name="author_affiliations" id="sidebar-affiliations" class="sidebar-textarea" rows="4"
```

Find (line ~321):
```blade
<textarea name="references" class="sidebar-textarea" rows="8"
```
Replace with:
```blade
<textarea name="references" id="sidebar-references" class="sidebar-textarea" rows="8"
```

Find (line ~330):
```blade
<textarea name="acknowledgements" class="sidebar-textarea" rows="3"
```
Replace with:
```blade
<textarea name="acknowledgements" id="sidebar-acknowledgements" class="sidebar-textarea" rows="3"
```

- [ ] **Step 3.2: Add title banner container**

In `resources/views/admin/submissions/layout.blade.php`, find the line `{{-- Editor --}}` (line ~271) and add a banner container just before the block editor include:

```blade
            {{-- Editor --}}
            <div class="layout-main">
                <div id="detected-title-banner" style="display:none; background:#eff6ff; border:1px solid #93c5fd; border-radius:0.5rem; padding:0.75rem 1rem; margin-bottom:1rem; font-size:0.85rem; color:#1e40af;">
                    <strong>Titre détecté :</strong> <span id="detected-title-text"></span>
                    <button type="button" id="update-title-btn" style="margin-left:0.75rem; background:#3b82f6; color:white; border:none; padding:0.25rem 0.75rem; border-radius:0.25rem; cursor:pointer; font-size:0.8rem;">
                        Mettre à jour le titre
                    </button>
                    <button type="button" onclick="document.getElementById('detected-title-banner').style.display='none'" style="margin-left:0.25rem; background:none; border:none; cursor:pointer; color:#6b7280; font-size:1rem;">&times;</button>
                </div>
                @include('admin.submissions._block-editor')
            </div>
```

- [ ] **Step 3.3: Add title update CSS**

In the `<style>` section, add:

```css
    #detected-title-banner {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    #detected-title-text {
        font-style: italic;
        flex: 1;
    }
```

- [ ] **Step 3.4: Commit**

```bash
git add resources/views/admin/submissions/layout.blade.php
git commit -m "feat(admin): layout sidebar IDs + detected title banner for enriched import"
```

---

## Task 4: Block editor JS — sidebar fill + title banner

**Files:**
- Modify: `resources/views/admin/submissions/_block-editor.blade.php`

---

- [ ] **Step 4.1: Update importMarkdown JS to handle enriched response**

In `resources/views/admin/submissions/_block-editor.blade.php`, replace the success handling block inside `importMarkdown()`. Find:

```javascript
                this.blocks = data.blocks;
                this.blockIdCounter = data.blocks.length + 1;
                alert('Import réussi : ' + data.count + ' blocs créés.');
```

Replace with:

```javascript
                this.blocks = data.blocks;
                this.blockIdCounter = data.blocks.length + 1;

                // Pre-fill sidebar fields if enriched response (Word/ODT)
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
                                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                                        || document.querySelector('input[name="_token"]')?.value
                                        || '{{ csrf_token() }}';
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
```

- [ ] **Step 4.2: Verify**

```bash
php artisan view:clear
vendor/bin/phpunit
```

Expected: all green.

- [ ] **Step 4.3: Commit**

```bash
git add resources/views/admin/submissions/_block-editor.blade.php
git commit -m "feat(admin): JS handles enriched import response (sidebar fill, title banner, Artemisiae links)"
```

---

## Task 5: Doc + full suite

**Files:**
- Modify: `resources/views/admin/documentation/index.blade.php`

---

- [ ] **Step 5.1: Update documentation**

In `resources/views/admin/documentation/index.blade.php`, find the "Conversion intelligente (Word/ODT)" section. Replace:

```blade
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

With:

```blade
                <h4>Conversion intelligente (Word/ODT)</h4>
                <p>Les fichiers Word et ODT sont convertis via l'API Claude (modèle Haiku). L'IA analyse la structure du document et extrait automatiquement :</p>
                <ul>
                    <li><strong>Corps de l'article</strong> — titres hiérarchisés, formatage, tableaux, listes. Le titre principal, les affiliations, les références et les remerciements sont <strong>retirés du corps</strong> et placés dans les champs dédiés.</li>
                    <li><strong>Références bibliographiques</strong> — extraites et pré-remplies dans le champ sidebar "Références bibliographiques"</li>
                    <li><strong>Affiliations auteurs</strong> — extraites et pré-remplies dans le champ sidebar "Affiliations auteurs"</li>
                    <li><strong>Remerciements</strong> — extraits et pré-remplis dans le champ sidebar "Remerciements"</li>
                    <li><strong>Noms de taxons</strong> — détectés automatiquement et enrichis avec un lien vers <a href="https://oreina.org/artemisiae/" target="_blank">Artemisiae</a> (au survol du nom en italique dans les blocs)</li>
                    <li><strong>Titre détecté</strong> — si le titre extrait diffère du titre de la soumission, un bandeau propose de le mettre à jour</li>
                </ul>

                <div class="doc-info">
                    <strong>Attention :</strong> l'import remplace tous les blocs existants et les champs sidebar (avec confirmation). Les images ne sont pas extraites des fichiers Word — elles sont envoyées séparément en haute résolution après acceptation.<br>
                    <strong>Prérequis Word/ODT :</strong> la conversion nécessite que la clé API Anthropic soit configurée (<code>ANTHROPIC_PLATFORM_KEY</code> dans <code>.env</code>). Si elle n'est pas configurée, un message invite à importer un fichier <code>.md</code> à la place.
                </div>
```

- [ ] **Step 5.2: Full suite**

```bash
php artisan view:clear
vendor/bin/phpunit
```

Expected: all green.

- [ ] **Step 5.3: Commit**

```bash
git add resources/views/admin/documentation/index.blade.php
git commit -m "docs(extranet): document enriched import (metadata extraction, taxon links, title detection)"
```

---

## Récapitulatif des commits

1. `feat(journal): DocumentConversionService.toStructured() returns JSON with metadata extraction`
2. `feat(admin): enriched import response with metadata, taxon links, and title update route`
3. `feat(admin): layout sidebar IDs + detected title banner for enriched import`
4. `feat(admin): JS handles enriched import response (sidebar fill, title banner, Artemisiae links)`
5. `docs(extranet): document enriched import (metadata extraction, taxon links, title detection)`

---

## Notes de vigilance

- **JSON vs Markdown fallback** : Claude peut parfois retourner du Markdown au lieu du JSON demandé, ou envelopper le JSON dans des balises ` ```json `. La méthode `parseStructuredResponse()` gère les deux cas : elle strip les code fences et fallback en traitant la réponse comme du Markdown brut si le JSON est invalide.
- **Regex taxons** : la regex pour les liens Artemisiae cherche `<em>Nom taxon</em>` qui n'est pas déjà dans un `<a>`. L'approche est un lookbehind/lookahead simplifié — si le HTML est complexe, certains cas edge peuvent ne pas être capturés. C'est acceptable : un taxon non lié est mieux qu'un HTML cassé.
- **MAX_TOKENS** : augmenté à 16384 pour le JSON structuré qui est plus verbeux que le Markdown brut.
- **Route PATCH titre** : protégée par le middleware `admin` existant (même groupe que les autres routes submissions).
- **Pas de Claude dans les commits**.
