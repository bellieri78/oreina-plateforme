<?php

namespace App\Services;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableCell;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\Table\TableRow;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;

class MarkdownToBlocksService
{
    private MarkdownParser $parser;
    private HtmlRenderer $renderer;
    private int $idCounter = 0;

    public function __construct()
    {
        $environment = new Environment([]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());

        $this->parser = new MarkdownParser($environment);
        $this->renderer = new HtmlRenderer($environment);
    }

    public function parse(string $markdown): array
    {
        $this->idCounter = 0;
        $document = $this->parser->parse($markdown);
        $blocks = [];

        foreach ($document->children() as $node) {
            $block = $this->nodeToBlock($node);
            if ($block !== null) {
                $blocks[] = $block;
            }
        }

        return $blocks;
    }

    private function nodeToBlock($node): ?array
    {
        if ($node instanceof Heading) {
            return [
                'id' => $this->generateId(),
                'type' => 'heading',
                'level' => (string) $node->getLevel(),
                'content' => $this->getInlineContent($node),
            ];
        }

        if ($node instanceof Paragraph) {
            $imageBlock = $this->extractImageBlock($node);
            if ($imageBlock) {
                return $imageBlock;
            }

            return [
                'id' => $this->generateId(),
                'type' => 'paragraph',
                'content' => $this->renderInlineHtml($node),
            ];
        }

        if ($node instanceof BlockQuote) {
            return [
                'id' => $this->generateId(),
                'type' => 'quote',
                'content' => $this->getTextContent($node),
                'source' => '',
            ];
        }

        if ($node instanceof ListBlock) {
            $items = [];
            foreach ($node->children() as $listItem) {
                if ($listItem instanceof ListItem) {
                    $items[] = trim($this->getTextContent($listItem));
                }
            }

            return [
                'id' => $this->generateId(),
                'type' => 'list',
                'listType' => $node->getListData()->type === ListBlock::TYPE_ORDERED ? 'ordered' : 'unordered',
                'items' => $items,
            ];
        }

        if ($node instanceof Table) {
            return $this->tableToBlock($node);
        }

        return null;
    }

    private function extractImageBlock(Paragraph $node): ?array
    {
        $children = [];
        foreach ($node->children() as $child) {
            $children[] = $child;
        }

        if (count($children) === 1 && $children[0] instanceof Image) {
            $image = $children[0];

            return [
                'id' => $this->generateId(),
                'type' => 'image',
                'url' => $image->getUrl(),
                'alt' => $this->getTextContent($image),
                'caption' => $image->getTitle() ?? '',
                'align' => 'center',
                'width' => 'auto',
            ];
        }

        return null;
    }

    private function tableToBlock(Table $table): array
    {
        $headers = [];
        $rows = [];
        $isFirst = true;

        foreach ($table->children() as $section) {
            foreach ($section->children() as $row) {
                if (! $row instanceof TableRow) {
                    continue;
                }

                $cells = [];
                foreach ($row->children() as $cell) {
                    if ($cell instanceof TableCell) {
                        $cells[] = trim($this->getTextContent($cell));
                    }
                }

                if ($isFirst) {
                    $headers = $cells;
                    $isFirst = false;
                } else {
                    $rows[] = $cells;
                }
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

    private function getInlineContent($node): string
    {
        $text = '';
        foreach ($node->children() as $child) {
            if ($child instanceof Text) {
                $text .= $child->getLiteral();
            } else {
                $text .= $this->getTextContent($child);
            }
        }

        return trim($text);
    }

    private function getTextContent($node): string
    {
        if ($node instanceof Text) {
            return $node->getLiteral();
        }

        $text = '';
        foreach ($node->children() as $child) {
            $text .= $this->getTextContent($child);
        }

        return $text;
    }

    private function renderInlineHtml($node): string
    {
        $html = $this->renderer->renderNodes([$node]);
        $html = trim(strip_tags($html, '<strong><em><code><a><br><sub><sup>'));
        $html = preg_replace('/^<p>|<\/p>$/i', '', trim($html));

        return trim($html);
    }

    private function generateId(): string
    {
        return 'block-md-' . (++$this->idCounter);
    }
}
