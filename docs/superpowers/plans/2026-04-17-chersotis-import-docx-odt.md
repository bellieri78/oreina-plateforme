# Chersotis — Import Word/ODT → blocs de maquette — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Ajouter l'import direct de fichiers `.docx` et `.odt` dans l'éditeur de blocs de maquettage, via `phpoffice/phpword`, sans dépendance système externe.

**Architecture :** `composer require phpoffice/phpword` + service `DocumentToBlocksService` qui parcourt l'arbre PhpWord (sections → éléments) et mappe chaque élément vers un bloc JSON. Le controller existant `importMarkdown` dispatch vers le bon service selon l'extension. Le bouton UI passe de "Importer Markdown" à "Importer un document".

**Tech Stack :** Laravel 12, PHPUnit 11, `phpoffice/phpword` v1+, Alpine.js.

**Spec :** `docs/superpowers/specs/2026-04-17-chersotis-import-docx-odt-design.md`

---

## File Structure

### Fichiers créés

| Chemin | Responsabilité |
|---|---|
| `app/Services/DocumentToBlocksService.php` | Parse .docx/.odt → array de blocs |
| `tests/Unit/Services/DocumentToBlocksServiceTest.php` | Tests TDD |

### Fichiers modifiés

| Chemin | Changement |
|---|---|
| `composer.json` | Ajout `phpoffice/phpword` |
| `app/Http/Controllers/Admin/SubmissionController.php` | Dispatch par extension dans `importMarkdown()` |
| `resources/views/admin/submissions/_block-editor.blade.php` | Label bouton + accept élargi |
| `resources/views/admin/documentation/index.blade.php` | Doc mise à jour |

---

## Task 1: Install phpword + DocumentToBlocksService (TDD)

**Files:**
- Create: `tests/Unit/Services/DocumentToBlocksServiceTest.php`
- Create: `app/Services/DocumentToBlocksService.php`

---

- [ ] **Step 1.1: Install phpoffice/phpword**

```bash
composer require phpoffice/phpword
```

Expected: installs successfully. Verify:

```bash
composer show phpoffice/phpword | head -3
```

- [ ] **Step 1.2: Write tests**

File `tests/Unit/Services/DocumentToBlocksServiceTest.php`:

```php
<?php

namespace Tests\Unit\Services;

use App\Services\DocumentToBlocksService;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PHPUnit\Framework\TestCase;

class DocumentToBlocksServiceTest extends TestCase
{
    private DocumentToBlocksService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DocumentToBlocksService();
    }

    private function createDocx(callable $builder): string
    {
        $phpWord = new PhpWord();
        $builder($phpWord);
        $path = tempnam(sys_get_temp_dir(), 'test_') . '.docx';
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($path);

        return $path;
    }

    private function createOdt(callable $builder): string
    {
        $phpWord = new PhpWord();
        $builder($phpWord);
        $path = tempnam(sys_get_temp_dir(), 'test_') . '.odt';
        $writer = IOFactory::createWriter($phpWord, 'ODText');
        $writer->save($path);

        return $path;
    }

    public function test_heading_by_style(): void
    {
        $path = $this->createDocx(function (PhpWord $pw) {
            $section = $pw->addSection();
            $section->addTitle('Introduction', 1);
            $section->addTitle('Méthode', 2);
            $section->addTitle('Résultats', 3);
        });

        $blocks = $this->service->parseFile($path);
        unlink($path);

        $this->assertCount(3, $blocks);
        $this->assertSame('heading', $blocks[0]['type']);
        $this->assertSame('1', $blocks[0]['level']);
        $this->assertSame('Introduction', $blocks[0]['content']);
        $this->assertSame('2', $blocks[1]['level']);
        $this->assertSame('3', $blocks[2]['level']);
    }

    public function test_heading_by_heuristic(): void
    {
        $path = $this->createDocx(function (PhpWord $pw) {
            $section = $pw->addSection();
            $section->addText('Grand titre', ['bold' => true, 'size' => 16]);
            $section->addText('Sous-titre', ['bold' => true, 'size' => 14]);
            $section->addText('Section', ['bold' => true, 'size' => 12]);
        });

        $blocks = $this->service->parseFile($path);
        unlink($path);

        $this->assertSame('heading', $blocks[0]['type']);
        $this->assertSame('1', $blocks[0]['level']);
        $this->assertSame('Grand titre', $blocks[0]['content']);

        $this->assertSame('heading', $blocks[1]['type']);
        $this->assertSame('2', $blocks[1]['level']);

        $this->assertSame('heading', $blocks[2]['type']);
        $this->assertSame('3', $blocks[2]['level']);
    }

    public function test_paragraph_with_inline_formatting(): void
    {
        $path = $this->createDocx(function (PhpWord $pw) {
            $section = $pw->addSection();
            $textRun = $section->addTextRun();
            $textRun->addText('Texte normal avec du ');
            $textRun->addText('gras', ['bold' => true]);
            $textRun->addText(' et de l\'');
            $textRun->addText('italique', ['italic' => true]);
            $textRun->addText('.');
        });

        $blocks = $this->service->parseFile($path);
        unlink($path);

        $this->assertCount(1, $blocks);
        $this->assertSame('paragraph', $blocks[0]['type']);
        $this->assertStringContainsString('<strong>gras</strong>', $blocks[0]['content']);
        $this->assertStringContainsString('<em>italique</em>', $blocks[0]['content']);
    }

    public function test_table(): void
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
            $table->addRow();
            $table->addCell()->addText('C. palaeno');
            $table->addCell()->addText('Vosges');
        });

        $blocks = $this->service->parseFile($path);
        unlink($path);

        $this->assertCount(1, $blocks);
        $this->assertSame('table', $blocks[0]['type']);
        $this->assertCount(2, $blocks[0]['headers']);
        $this->assertSame('Espèce', $blocks[0]['headers'][0]);
        $this->assertCount(2, $blocks[0]['rows']);
        $this->assertSame('P. apollo', $blocks[0]['rows'][0][0]);
    }

    public function test_list_unordered(): void
    {
        $path = $this->createDocx(function (PhpWord $pw) {
            $section = $pw->addSection();
            $section->addListItem('Premier élément', 0, null, ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED]);
            $section->addListItem('Deuxième élément', 0, null, ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED]);
            $section->addListItem('Troisième', 0, null, ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED]);
        });

        $blocks = $this->service->parseFile($path);
        unlink($path);

        $this->assertCount(1, $blocks);
        $this->assertSame('list', $blocks[0]['type']);
        $this->assertSame('unordered', $blocks[0]['listType']);
        $this->assertCount(3, $blocks[0]['items']);
        $this->assertSame('Premier élément', $blocks[0]['items'][0]);
    }

    public function test_list_ordered(): void
    {
        $path = $this->createDocx(function (PhpWord $pw) {
            $section = $pw->addSection();
            $section->addListItem('Un', 0, null, ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER]);
            $section->addListItem('Deux', 0, null, ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER]);
            $section->addListItem('Trois', 0, null, ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER]);
        });

        $blocks = $this->service->parseFile($path);
        unlink($path);

        $this->assertCount(1, $blocks);
        $this->assertSame('list', $blocks[0]['type']);
        $this->assertSame('ordered', $blocks[0]['listType']);
        $this->assertCount(3, $blocks[0]['items']);
    }

    public function test_empty_paragraphs_skipped(): void
    {
        $path = $this->createDocx(function (PhpWord $pw) {
            $section = $pw->addSection();
            $section->addText('Contenu réel');
            $section->addText('');
            $section->addText('Autre contenu');
        });

        $blocks = $this->service->parseFile($path);
        unlink($path);

        $this->assertCount(2, $blocks);
        $this->assertSame('Contenu réel', strip_tags($blocks[0]['content']));
        $this->assertSame('Autre contenu', strip_tags($blocks[1]['content']));
    }

    public function test_each_block_has_id(): void
    {
        $path = $this->createDocx(function (PhpWord $pw) {
            $section = $pw->addSection();
            $section->addText('Paragraphe un');
            $section->addText('Paragraphe deux');
        });

        $blocks = $this->service->parseFile($path);
        unlink($path);

        foreach ($blocks as $block) {
            $this->assertArrayHasKey('id', $block);
            $this->assertNotEmpty($block['id']);
        }

        // IDs are unique
        $ids = array_column($blocks, 'id');
        $this->assertCount(count($ids), array_unique($ids));
    }

    public function test_odt_format(): void
    {
        $path = $this->createOdt(function (PhpWord $pw) {
            $section = $pw->addSection();
            $section->addText('Contenu ODT');
        });

        $blocks = $this->service->parseFile($path);
        unlink($path);

        $this->assertCount(1, $blocks);
        $this->assertSame('paragraph', $blocks[0]['type']);
        $this->assertStringContainsString('Contenu ODT', $blocks[0]['content']);
    }

    public function test_mixed_document(): void
    {
        $path = $this->createDocx(function (PhpWord $pw) {
            $section = $pw->addSection();
            $section->addTitle('Introduction', 1);
            $section->addText('Un paragraphe d\'introduction.');
            $section->addTitle('Méthode', 2);
            $section->addListItem('Point A', 0, null, ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED]);
            $section->addListItem('Point B', 0, null, ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED]);
            $table = $section->addTable();
            $table->addRow();
            $table->addCell()->addText('Col1');
            $table->addCell()->addText('Col2');
            $table->addRow();
            $table->addCell()->addText('A');
            $table->addCell()->addText('B');
        });

        $blocks = $this->service->parseFile($path);
        unlink($path);

        $types = array_column($blocks, 'type');
        $this->assertContains('heading', $types);
        $this->assertContains('paragraph', $types);
        $this->assertContains('list', $types);
        $this->assertContains('table', $types);
        $this->assertGreaterThanOrEqual(4, count($blocks));
    }
}
```

- [ ] **Step 1.3: Run (fail)**

```bash
vendor/bin/phpunit tests/Unit/Services/DocumentToBlocksServiceTest.php
```

Expected: `Class "App\Services\DocumentToBlocksService" not found`.

- [ ] **Step 1.4: Implement**

File `app/Services/DocumentToBlocksService.php`:

```php
<?php

namespace App\Services;

use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\Element\ListItem;
use PhpOffice\PhpWord\Element\ListItemRun;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\Title;
use PhpOffice\PhpWord\IOFactory;

class DocumentToBlocksService
{
    private int $idCounter = 0;

    public function parseFile(string $filePath): array
    {
        $this->idCounter = 0;
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $reader = match ($ext) {
            'docx' => 'Word2007',
            'odt'  => 'ODText',
            default => throw new \InvalidArgumentException("Format non supporté: .{$ext}"),
        };

        $phpWord = IOFactory::load($filePath, $reader);

        $blocks = [];
        $pendingListItems = [];
        $pendingListType = null;

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                // List items: accumulate consecutive items into one block
                if ($element instanceof ListItem || $element instanceof ListItemRun) {
                    $item = $this->extractListItemText($element);
                    $type = $this->detectListType($element);

                    if ($pendingListItems && $pendingListType !== $type) {
                        $blocks[] = $this->buildListBlock($pendingListItems, $pendingListType);
                        $pendingListItems = [];
                    }

                    $pendingListItems[] = $item;
                    $pendingListType = $type;
                    continue;
                }

                // Flush pending list items before processing non-list element
                if ($pendingListItems) {
                    $blocks[] = $this->buildListBlock($pendingListItems, $pendingListType);
                    $pendingListItems = [];
                    $pendingListType = null;
                }

                $block = $this->elementToBlock($element);
                if ($block !== null) {
                    $blocks[] = $block;
                }
            }
        }

        // Flush remaining list items
        if ($pendingListItems) {
            $blocks[] = $this->buildListBlock($pendingListItems, $pendingListType);
        }

        return $blocks;
    }

    private function elementToBlock(AbstractElement $element): ?array
    {
        if ($element instanceof Title) {
            $depth = $element->getDepth();
            return [
                'id' => $this->generateId(),
                'type' => 'heading',
                'level' => (string) min($depth, 3),
                'content' => $this->extractText($element->getText()),
            ];
        }

        if ($element instanceof Table) {
            return $this->tableToBlock($element);
        }

        if ($element instanceof TextRun) {
            return $this->textRunToBlock($element);
        }

        if ($element instanceof Text) {
            return $this->singleTextToBlock($element);
        }

        return null;
    }

    private function textRunToBlock(TextRun $textRun): ?array
    {
        $plainText = '';
        $html = '';

        foreach ($textRun->getElements() as $child) {
            if ($child instanceof Text) {
                $text = $child->getText();
                $plainText .= $text;
                $html .= $this->wrapInlineFormatting($text, $child->getFontStyle());
            }
        }

        $plainText = trim($plainText);
        if ($plainText === '') {
            return null;
        }

        // Heading heuristic: short + all bold + large font
        $headingLevel = $this->detectHeadingHeuristic($textRun, $plainText);
        if ($headingLevel !== null) {
            return [
                'id' => $this->generateId(),
                'type' => 'heading',
                'level' => (string) $headingLevel,
                'content' => $plainText,
            ];
        }

        return [
            'id' => $this->generateId(),
            'type' => 'paragraph',
            'content' => trim($html),
        ];
    }

    private function singleTextToBlock(Text $text): ?array
    {
        $content = trim($text->getText());
        if ($content === '') {
            return null;
        }

        // Heading heuristic for single Text element
        $fontStyle = $text->getFontStyle();
        $isBold = false;
        $fontSize = null;

        if (is_object($fontStyle)) {
            $isBold = $fontStyle->isBold();
            $fontSize = $fontStyle->getSize();
        } elseif (is_array($fontStyle)) {
            $isBold = $fontStyle['bold'] ?? false;
            $fontSize = $fontStyle['size'] ?? null;
        }

        if (mb_strlen($content) < 100 && $isBold) {
            $level = $this->fontSizeToHeadingLevel($fontSize);
            return [
                'id' => $this->generateId(),
                'type' => 'heading',
                'level' => (string) $level,
                'content' => $content,
            ];
        }

        $html = $this->wrapInlineFormatting($content, $fontStyle);

        return [
            'id' => $this->generateId(),
            'type' => 'paragraph',
            'content' => trim($html),
        ];
    }

    private function detectHeadingHeuristic(TextRun $textRun, string $plainText): ?int
    {
        if (mb_strlen($plainText) >= 100) {
            return null;
        }

        $allBold = true;
        $maxFontSize = null;

        foreach ($textRun->getElements() as $child) {
            if (!$child instanceof Text) {
                continue;
            }

            $fontStyle = $child->getFontStyle();
            $isBold = false;
            $size = null;

            if (is_object($fontStyle)) {
                $isBold = $fontStyle->isBold();
                $size = $fontStyle->getSize();
            } elseif (is_array($fontStyle)) {
                $isBold = $fontStyle['bold'] ?? false;
                $size = $fontStyle['size'] ?? null;
            }

            if (!$isBold) {
                $allBold = false;
                break;
            }

            if ($size !== null && ($maxFontSize === null || $size > $maxFontSize)) {
                $maxFontSize = $size;
            }
        }

        if (!$allBold) {
            return null;
        }

        return $this->fontSizeToHeadingLevel($maxFontSize);
    }

    private function fontSizeToHeadingLevel(?float $size): int
    {
        if ($size === null) {
            return 3;
        }
        if ($size >= 16) {
            return 1;
        }
        if ($size >= 14) {
            return 2;
        }

        return 3;
    }

    private function tableToBlock(Table $table): array
    {
        $headers = [];
        $rows = [];
        $isFirst = true;

        foreach ($table->getRows() as $row) {
            $cells = [];
            foreach ($row->getCells() as $cell) {
                $cellText = '';
                foreach ($cell->getElements() as $el) {
                    $cellText .= $this->extractText($el);
                }
                $cells[] = trim($cellText);
            }

            if ($isFirst) {
                $headers = $cells;
                $isFirst = false;
            } else {
                $rows[] = $cells;
            }
        }

        return [
            'id' => $this->generateId(),
            'type' => 'table',
            'headers' => $headers,
            'rows' => $rows,
            'caption' => '',
        ];
    }

    private function buildListBlock(array $items, string $type): array
    {
        return [
            'id' => $this->generateId(),
            'type' => 'list',
            'listType' => $type,
            'items' => $items,
        ];
    }

    private function extractListItemText(ListItem|ListItemRun $element): string
    {
        if ($element instanceof ListItem) {
            return $this->extractText($element->getTextObject());
        }

        // ListItemRun contains child elements
        $text = '';
        foreach ($element->getElements() as $child) {
            $text .= $this->extractText($child);
        }

        return trim($text);
    }

    private function detectListType(ListItem|ListItemRun $element): string
    {
        $style = null;

        if ($element instanceof ListItem) {
            $style = $element->getStyle();
        } elseif ($element instanceof ListItemRun) {
            $style = $element->getStyle();
        }

        if ($style !== null) {
            $numStyle = $style->getNumStyle();
            if ($numStyle !== null && (
                str_contains(strtolower($numStyle), 'number')
                || str_contains(strtolower($numStyle), 'decimal')
            )) {
                return 'ordered';
            }

            $listType = $style->getListType();
            if ($listType !== null) {
                // PhpWord TYPE_NUMBER = 7, TYPE_NUMBER_NESTED = 8
                if ($listType >= \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER) {
                    return 'ordered';
                }
            }
        }

        return 'unordered';
    }

    private function extractText($element): string
    {
        if (is_string($element)) {
            return $element;
        }

        if ($element instanceof Text) {
            return $element->getText();
        }

        if ($element instanceof TextRun) {
            $text = '';
            foreach ($element->getElements() as $child) {
                $text .= $this->extractText($child);
            }
            return $text;
        }

        if ($element instanceof Title) {
            return $this->extractText($element->getText());
        }

        return '';
    }

    private function wrapInlineFormatting(string $text, $fontStyle): string
    {
        if ($text === '') {
            return '';
        }

        $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $isBold = false;
        $isItalic = false;
        $isSuperScript = false;
        $isSubScript = false;

        if (is_object($fontStyle)) {
            $isBold = $fontStyle->isBold();
            $isItalic = $fontStyle->isItalic();
            $isSuperScript = $fontStyle->isSuperScript();
            $isSubScript = $fontStyle->isSubScript();
        } elseif (is_array($fontStyle)) {
            $isBold = $fontStyle['bold'] ?? false;
            $isItalic = $fontStyle['italic'] ?? false;
            $isSuperScript = $fontStyle['superScript'] ?? false;
            $isSubScript = $fontStyle['subScript'] ?? false;
        }

        if ($isSuperScript) {
            $escaped = '<sup>' . $escaped . '</sup>';
        }
        if ($isSubScript) {
            $escaped = '<sub>' . $escaped . '</sub>';
        }
        if ($isBold) {
            $escaped = '<strong>' . $escaped . '</strong>';
        }
        if ($isItalic) {
            $escaped = '<em>' . $escaped . '</em>';
        }

        return $escaped;
    }

    private function generateId(): string
    {
        return 'block-doc-' . (++$this->idCounter);
    }
}
```

- [ ] **Step 1.5: Run (pass)**

```bash
vendor/bin/phpunit tests/Unit/Services/DocumentToBlocksServiceTest.php
```

Expected: `OK (11 tests, ...)`.

If tests fail because of PhpWord API differences (list style detection, title depth, font style object vs array), adapt the implementation. The most common issues:

1. `Title::getDepth()` — verify the method exists. If it returns 0-based, add +1.
2. `ListItem::getStyle()` — may return `null` if no explicit style. The `detectListType` method handles this with a null check.
3. Font style may be a string (named style) instead of object or array — add a string check in `wrapInlineFormatting` and `singleTextToBlock`.

- [ ] **Step 1.6: Commit**

```bash
git add composer.json composer.lock app/Services/DocumentToBlocksService.php tests/Unit/Services/DocumentToBlocksServiceTest.php
git commit -m "feat(journal): DocumentToBlocksService with phpoffice/phpword (heading, paragraph, table, list)"
```

---

## Task 2: Controller dispatch + UI update

**Files:**
- Modify: `app/Http/Controllers/Admin/SubmissionController.php`
- Modify: `resources/views/admin/submissions/_block-editor.blade.php`

---

- [ ] **Step 2.1: Update controller**

In `app/Http/Controllers/Admin/SubmissionController.php`, add the import at the top (after the existing `MarkdownToBlocksService` import):

```php
use App\Services\DocumentToBlocksService;
```

Then replace the `importMarkdown` method (starting at line ~752) with:

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

        if (in_array($ext, $markdownExts, true)) {
            $content = file_get_contents($file->getRealPath());
            $blocks = app(MarkdownToBlocksService::class)->parse($content);
        } else {
            $blocks = app(DocumentToBlocksService::class)->parseFile($file->getRealPath());
        }

        return response()->json([
            'blocks' => $blocks,
            'count' => count($blocks),
        ]);
    }
```

- [ ] **Step 2.2: Update UI — button label + accepted extensions**

In `resources/views/admin/submissions/_block-editor.blade.php`, find the import button (line ~273). Replace:

```blade
        <button type="button" @click="$refs.mdFileInput.click()" class="add-block-btn" style="background:#6366f1;color:white;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            Importer Markdown
        </button>
        <input type="file" x-ref="mdFileInput" accept=".md,.txt,.markdown" style="display:none"
               @change="importMarkdown($event)">
```

With:

```blade
        <button type="button" @click="$refs.mdFileInput.click()" class="add-block-btn" style="background:#6366f1;color:white;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            Importer un document
        </button>
        <input type="file" x-ref="mdFileInput" accept=".md,.txt,.markdown,.docx,.odt" style="display:none"
               @change="importMarkdown($event)">
```

- [ ] **Step 2.3: Verify**

```bash
php artisan view:clear
php artisan route:list --name=import-markdown
vendor/bin/phpunit
```

Expected: route listed, all tests green.

- [ ] **Step 2.4: Commit**

```bash
git add app/Http/Controllers/Admin/SubmissionController.php \
        resources/views/admin/submissions/_block-editor.blade.php
git commit -m "feat(admin): import Word/ODT documents in block editor (dispatch by extension)"
```

---

## Task 3: Doc + full suite

**Files:**
- Modify: `resources/views/admin/documentation/index.blade.php`

---

- [ ] **Step 3.1: Update documentation**

In `resources/views/admin/documentation/index.blade.php`, find the "Import Markdown" section (line ~1808). Replace:

```blade
                <h3>Import Markdown</h3>
                <p>Dans la page de maquettage d'un article (<code>/extranet/submissions/{id}/layout</code>), un bouton <strong>"Importer Markdown"</strong> (violet) permet d'uploader un fichier <code>.md</code> ou <code>.txt</code> qui est automatiquement converti en blocs de maquette.</p>

                <h4>Types de blocs générés</h4>
                <ul>
                    <li><code># / ## / ###</code> → bloc Titre (H1 / H2 / H3)</li>
                    <li>Paragraphe avec <code>**gras**</code> / <code>*italique*</code> → bloc Paragraphe (HTML inline conservé)</li>
                    <li><code>![alt](url)</code> → bloc Image (URL conservée, upload séparé si nécessaire)</li>
                    <li><code>&gt; citation</code> → bloc Citation</li>
                    <li><code>- item</code> / <code>1. item</code> → bloc Liste (ordonnée ou non)</li>
                    <li>Tableau pipe <code>| col1 | col2 |</code> → bloc Tableau (headers + lignes)</li>
                </ul>

                <div class="doc-info">
                    <strong>Attention :</strong> l'import remplace tous les blocs existants (avec confirmation). Les images référencées dans le Markdown doivent être des URLs accessibles — l'upload d'images séparées se fait via l'éditeur de blocs après import.<br>
                    <strong>Formats acceptés :</strong> <code>.md</code>, <code>.txt</code>, <code>.markdown</code> — max 5 Mo.
                </div>
```

With:

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

- [ ] **Step 3.2: Full suite**

```bash
php artisan view:clear
vendor/bin/phpunit
```

Expected: all green (152 + 11 = 163 tests).

- [ ] **Step 3.3: Commit**

```bash
git add resources/views/admin/documentation/index.blade.php
git commit -m "docs(extranet): document Word/ODT import in block editor"
```

---

## Récapitulatif des commits

1. `feat(journal): DocumentToBlocksService with phpoffice/phpword (heading, paragraph, table, list)`
2. `feat(admin): import Word/ODT documents in block editor (dispatch by extension)`
3. `docs(extranet): document Word/ODT import in block editor`

---

## Notes de vigilance

- **PhpWord API** : la lib a des différences entre `Text` (élément simple) et `TextRun` (paragraphe avec formatage mixte). Les deux cas sont gérés séparément.
- **Styles Word vs heuristique** : `Title` (style natif) est prioritaire. L'heuristique ne s'applique qu'aux `TextRun` et `Text` sans style `Title`.
- **Listes** : PhpWord expose les listes comme des `ListItem` consécutifs, pas comme un nœud parent. Le service les accumule et les flushe en un seul bloc `list` quand un élément non-liste arrive.
- **ODT** : PhpWord utilise le même arbre d'objets pour `.docx` et `.odt` — seul le reader change (`Word2007` vs `ODText`).
- **Taille max** : 5 Mo (validation côté controller, identique au Markdown).
- **Pas de `.doc`** : le format binaire ancien n'est pas supporté par PhpWord.
- **Pas de Claude dans les commits**.
