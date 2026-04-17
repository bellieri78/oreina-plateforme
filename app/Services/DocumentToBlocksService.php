<?php

namespace App\Services;

use PhpOffice\PhpWord\Element\AbstractElement;
use PhpOffice\PhpWord\Element\ListItem;
use PhpOffice\PhpWord\Element\ListItemRun;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextBreak;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Title;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;

/**
 * Converts .docx / .odt files into the block editor block array format.
 *
 * Supported block types: heading, paragraph, table, list.
 * Images are silently ignored; empty paragraphs are skipped.
 */
class DocumentToBlocksService
{
    private int $blockCounter = 0;

    /**
     * Parse a .docx or .odt file and return an array of blocks.
     *
     * @param  string $filePath  Absolute path to the document.
     * @return array<int, array<string, mixed>>
     */
    public function parseFile(string $filePath): array
    {
        $this->blockCounter = 0;

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $readerName = $extension === 'odt' ? 'ODText' : 'Word2007';

        $phpWord = IOFactory::load($filePath, $readerName);

        $blocks = [];

        foreach ($phpWord->getSections() as $section) {
            $pendingList = null;

            foreach ($section->getElements() as $element) {
                if ($element instanceof Title) {
                    $blocks = $this->flushList($blocks, $pendingList);
                    $pendingList = null;
                    $block = $this->convertTitle($element);
                    if ($block !== null) {
                        $blocks[] = $block;
                    }
                    continue;
                }

                if ($element instanceof ListItem || $element instanceof ListItemRun) {
                    $item = $this->extractListItemText($element);
                    $listType = $this->detectListType($element);

                    if ($pendingList === null) {
                        $pendingList = ['type' => 'list', 'listType' => $listType, 'items' => []];
                    }
                    $pendingList['items'][] = $item;
                    continue;
                }

                // Non-list element: flush any pending list first
                $blocks = $this->flushList($blocks, $pendingList);
                $pendingList = null;

                if ($element instanceof Table) {
                    $block = $this->convertTable($element);
                    if ($block !== null) {
                        $blocks[] = $block;
                    }
                    continue;
                }

                if ($element instanceof TextRun) {
                    $block = $this->convertTextRun($element);
                    if ($block !== null) {
                        $blocks[] = $block;
                    }
                    continue;
                }

                if ($element instanceof Text) {
                    $block = $this->convertText($element);
                    if ($block !== null) {
                        $blocks[] = $block;
                    }
                    continue;
                }

                // TextBreak, Image, etc. → skip
            }

            // Flush any trailing list
            $blocks = $this->flushList($blocks, $pendingList);
        }

        return $blocks;
    }

    // -------------------------------------------------------------------------
    // Element converters
    // -------------------------------------------------------------------------

    /**
     * Convert a Title element → heading block.
     *
     * @return array<string, mixed>|null
     */
    private function convertTitle(Title $title): ?array
    {
        $text = $title->getText();
        $content = is_string($text) ? $text : $this->extractTextRunContent($text);

        if (trim($content) === '') {
            return null;
        }

        $depth = $title->getDepth();
        $level = (string) max(1, min(3, $depth));

        return $this->makeBlock('heading', ['level' => $level, 'content' => $content]);
    }

    /**
     * Convert a TextRun element → heading (heuristic) or paragraph block.
     *
     * @return array<string, mixed>|null
     */
    private function convertTextRun(TextRun $textRun): ?array
    {
        $children = $textRun->getElements();

        if (empty($children)) {
            return null;
        }

        // Try heading heuristic: all children bold + short total text
        $html = $this->buildInlineHtml($children);
        $plainText = strip_tags($html);

        if (trim($plainText) === '') {
            return null;
        }

        if ($this->isHeadingHeuristic($children, $plainText)) {
            $level = $this->detectHeadingLevel($children);
            return $this->makeBlock('heading', ['level' => $level, 'content' => $plainText]);
        }

        return $this->makeBlock('paragraph', ['content' => $html]);
    }

    /**
     * Convert a Text element → heading (heuristic) or paragraph block.
     *
     * @return array<string, mixed>|null
     */
    private function convertText(Text $text): ?array
    {
        $content = (string) ($text->getText() ?? '');

        if (trim($content) === '') {
            return null;
        }

        $fontStyle = $text->getFontStyle();

        if ($this->isHeadingHeuristic([$text], $content)) {
            $level = $this->detectHeadingLevel([$text]);
            return $this->makeBlock('heading', ['level' => $level, 'content' => $content]);
        }

        $html = $this->applyInlineFormatting($content, $fontStyle);
        return $this->makeBlock('paragraph', ['content' => $html]);
    }

    /**
     * Convert a Table element → table block.
     *
     * @return array<string, mixed>|null
     */
    private function convertTable(Table $table): ?array
    {
        $rows = $table->getRows();

        if (empty($rows)) {
            return null;
        }

        $headers = $this->extractRowCellTexts($rows[0]);
        $dataRows = [];

        for ($i = 1; $i < count($rows); $i++) {
            $dataRows[] = $this->extractRowCellTexts($rows[$i]);
        }

        return $this->makeBlock('table', ['headers' => $headers, 'rows' => $dataRows]);
    }

    // -------------------------------------------------------------------------
    // List helpers
    // -------------------------------------------------------------------------

    /**
     * Flush a pending list into the blocks array and assign its ID.
     *
     * @param  array<int, array<string, mixed>>      $blocks
     * @param  array<string, mixed>|null             $pendingList
     * @return array<int, array<string, mixed>>
     */
    private function flushList(array $blocks, ?array $pendingList): array
    {
        if ($pendingList !== null && !empty($pendingList['items'])) {
            $pendingList['id'] = $this->nextId();
            $blocks[] = $pendingList;
        }

        return $blocks;
    }

    /**
     * Extract plain text from a ListItem or ListItemRun.
     */
    private function extractListItemText(AbstractElement $element): string
    {
        if ($element instanceof ListItem) {
            return (string) ($element->getText() ?? '');
        }

        if ($element instanceof ListItemRun) {
            $children = $element->getElements();
            $parts = [];
            foreach ($children as $child) {
                if ($child instanceof Text) {
                    $parts[] = (string) ($child->getText() ?? '');
                }
            }
            return implode('', $parts);
        }

        return '';
    }

    /**
     * Detect whether a list item is ordered or unordered.
     *
     * Strategy (in priority order):
     *  1. ListItem with legacy listType (TYPE_NUMBER / TYPE_NUMBER_NESTED / TYPE_ALPHANUM)
     *  2. Look up the numbering style name in the PhpWord Style registry and inspect
     *     the format of the first level: non-bullet formats → ordered.
     */
    private function detectListType(AbstractElement $element): string
    {
        $style = null;

        if ($element instanceof ListItem) {
            $style = $element->getStyle();
        } elseif ($element instanceof ListItemRun) {
            $style = $element->getStyle();
        }

        if ($style === null) {
            return 'unordered';
        }

        // Legacy list type check (pre-0.10.0 documents)
        $listType = $style->getListType();
        if (in_array($listType, [
            \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER,
            \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER_NESTED,
            \PhpOffice\PhpWord\Style\ListItem::TYPE_ALPHANUM,
        ], true)) {
            return 'ordered';
        }

        // Modern numbering: look up the numbering style in the registry
        $numStyleName = $style->getNumStyle();
        if ($numStyleName !== null && $numStyleName !== '') {
            $registeredStyle = \PhpOffice\PhpWord\Style::getStyle($numStyleName);
            if ($registeredStyle instanceof \PhpOffice\PhpWord\Style\Numbering) {
                $levels = $registeredStyle->getLevels();
                if (!empty($levels)) {
                    $firstLevel = reset($levels);
                    $format = $firstLevel->getFormat();
                    // 'bullet' means unordered; anything else (decimal, lowerLetter, etc.) means ordered
                    if ($format !== null && $format !== 'bullet') {
                        return 'ordered';
                    }
                }
            }
        }

        return 'unordered';
    }

    // -------------------------------------------------------------------------
    // Inline formatting / heuristics
    // -------------------------------------------------------------------------

    /**
     * Build inline HTML from an array of Text children.
     *
     * @param  AbstractElement[] $children
     */
    private function buildInlineHtml(array $children): string
    {
        $html = '';
        foreach ($children as $child) {
            if ($child instanceof Text) {
                $text = (string) ($child->getText() ?? '');
                $html .= $this->applyInlineFormatting($text, $child->getFontStyle());
            }
        }
        return $html;
    }

    /**
     * Wrap $text with HTML tags according to a Font style (object, array, or string).
     *
     * @param  Font|string|array<string, mixed>|null $fontStyle
     */
    private function applyInlineFormatting(string $text, mixed $fontStyle): string
    {
        if (!$fontStyle instanceof Font) {
            return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        $encoded = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        if ($fontStyle->isSuperScript()) {
            $encoded = "<sup>{$encoded}</sup>";
        } elseif ($fontStyle->isSubScript()) {
            $encoded = "<sub>{$encoded}</sub>";
        }

        if ($fontStyle->isItalic()) {
            $encoded = "<em>{$encoded}</em>";
        }

        if ($fontStyle->isBold()) {
            $encoded = "<strong>{$encoded}</strong>";
        }

        return $encoded;
    }

    /**
     * Determine if the given elements form a heading by heuristic.
     * Criteria: text is short (<100 chars) AND all runs are bold.
     *
     * @param  AbstractElement[] $elements
     */
    private function isHeadingHeuristic(array $elements, string $plainText): bool
    {
        if (strlen($plainText) >= 100) {
            return false;
        }

        foreach ($elements as $element) {
            if (!($element instanceof Text)) {
                return false;
            }
            $fontStyle = $element->getFontStyle();
            if (!$fontStyle instanceof Font) {
                return false;
            }
            if (!$fontStyle->isBold()) {
                return false;
            }
        }

        return !empty($elements);
    }

    /**
     * Detect the heading level (H1/H2/H3) from font size in elements.
     *
     * @param  AbstractElement[] $elements
     */
    private function detectHeadingLevel(array $elements): string
    {
        $maxSize = 0;

        foreach ($elements as $element) {
            if ($element instanceof Text) {
                $fontStyle = $element->getFontStyle();
                if ($fontStyle instanceof Font) {
                    $size = $fontStyle->getSize();
                    if ($size !== null && $size > $maxSize) {
                        $maxSize = (float) $size;
                    }
                }
            }
        }

        if ($maxSize >= 16) {
            return '1';
        }

        if ($maxSize >= 14) {
            return '2';
        }

        return '3';
    }

    // -------------------------------------------------------------------------
    // Table helpers
    // -------------------------------------------------------------------------

    /**
     * Extract plain text from each cell in a Row.
     *
     * @return string[]
     */
    private function extractRowCellTexts(\PhpOffice\PhpWord\Element\Row $row): array
    {
        $cellTexts = [];
        foreach ($row->getCells() as $cell) {
            $parts = [];
            foreach ($cell->getElements() as $element) {
                if ($element instanceof Text) {
                    $parts[] = (string) ($element->getText() ?? '');
                } elseif ($element instanceof TextRun) {
                    $children = $element->getElements();
                    foreach ($children as $child) {
                        if ($child instanceof Text) {
                            $parts[] = (string) ($child->getText() ?? '');
                        }
                    }
                }
            }
            $cellTexts[] = implode('', $parts);
        }
        return $cellTexts;
    }

    // -------------------------------------------------------------------------
    // TextRun text extraction (for Title with TextRun content)
    // -------------------------------------------------------------------------

    /**
     * Extract plain text from a TextRun (used for Title elements that contain a TextRun).
     */
    private function extractTextRunContent(TextRun $textRun): string
    {
        $parts = [];
        foreach ($textRun->getElements() as $element) {
            if ($element instanceof Text) {
                $parts[] = (string) ($element->getText() ?? '');
            }
        }
        return implode('', $parts);
    }

    // -------------------------------------------------------------------------
    // Block factory
    // -------------------------------------------------------------------------

    /**
     * Build a block array with an auto-incremented ID.
     *
     * @param  array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function makeBlock(string $type, array $data): array
    {
        return array_merge(['id' => $this->nextId(), 'type' => $type], $data);
    }

    /**
     * Return the next block ID string.
     */
    private function nextId(): string
    {
        $this->blockCounter++;
        return 'block-doc-' . $this->blockCounter;
    }
}
