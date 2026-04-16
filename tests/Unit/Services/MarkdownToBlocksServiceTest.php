<?php

namespace Tests\Unit\Services;

use App\Services\MarkdownToBlocksService;
use PHPUnit\Framework\TestCase;

class MarkdownToBlocksServiceTest extends TestCase
{
    private MarkdownToBlocksService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new MarkdownToBlocksService();
    }

    public function test_heading_levels(): void
    {
        $blocks = $this->service->parse("# Titre 1\n\n## Titre 2\n\n### Titre 3");
        $this->assertCount(3, $blocks);
        $this->assertSame('heading', $blocks[0]['type']);
        $this->assertSame('1', $blocks[0]['level']);
        $this->assertSame('Titre 1', $blocks[0]['content']);
        $this->assertSame('2', $blocks[1]['level']);
        $this->assertSame('3', $blocks[2]['level']);
    }

    public function test_paragraph_with_inline_formatting(): void
    {
        $blocks = $this->service->parse("Ceci est un paragraphe avec du **gras** et de l'*italique*.");
        $this->assertCount(1, $blocks);
        $this->assertSame('paragraph', $blocks[0]['type']);
        $this->assertStringContainsString('<strong>gras</strong>', $blocks[0]['content']);
        $this->assertStringContainsString('<em>italique</em>', $blocks[0]['content']);
    }

    public function test_image(): void
    {
        $blocks = $this->service->parse("![Description de l'image](https://example.com/photo.jpg)");
        $this->assertCount(1, $blocks);
        $this->assertSame('image', $blocks[0]['type']);
        $this->assertSame('https://example.com/photo.jpg', $blocks[0]['url']);
        $this->assertSame("Description de l'image", $blocks[0]['alt']);
    }

    public function test_blockquote(): void
    {
        $blocks = $this->service->parse("> Ceci est une citation importante.");
        $this->assertCount(1, $blocks);
        $this->assertSame('quote', $blocks[0]['type']);
        $this->assertStringContainsString('citation importante', $blocks[0]['content']);
    }

    public function test_unordered_list(): void
    {
        $blocks = $this->service->parse("- Premier élément\n- Deuxième élément\n- Troisième");
        $this->assertCount(1, $blocks);
        $this->assertSame('list', $blocks[0]['type']);
        $this->assertSame('unordered', $blocks[0]['listType']);
        $this->assertCount(3, $blocks[0]['items']);
        $this->assertSame('Premier élément', $blocks[0]['items'][0]);
    }

    public function test_ordered_list(): void
    {
        $blocks = $this->service->parse("1. Un\n2. Deux\n3. Trois");
        $this->assertCount(1, $blocks);
        $this->assertSame('list', $blocks[0]['type']);
        $this->assertSame('ordered', $blocks[0]['listType']);
        $this->assertCount(3, $blocks[0]['items']);
    }

    public function test_table(): void
    {
        $blocks = $this->service->parse("| Espèce | Localité |\n|--------|----------|\n| P. apollo | Pyrénées |\n| C. palaeno | Vosges |");
        $this->assertCount(1, $blocks);
        $this->assertSame('table', $blocks[0]['type']);
        $this->assertCount(2, $blocks[0]['headers']);
        $this->assertSame('Espèce', $blocks[0]['headers'][0]);
        $this->assertCount(2, $blocks[0]['rows']);
        $this->assertSame('P. apollo', $blocks[0]['rows'][0][0]);
    }

    public function test_mixed_document(): void
    {
        $md = "# Introduction\n\nParagraphe d'intro.\n\n## Méthode\n\n- Point 1\n- Point 2\n\n> Note importante\n\n| Col1 | Col2 |\n|------|------|\n| A | B |";
        $blocks = $this->service->parse($md);
        $types = array_column($blocks, 'type');
        $this->assertContains('heading', $types);
        $this->assertContains('paragraph', $types);
        $this->assertContains('list', $types);
        $this->assertContains('quote', $types);
        $this->assertContains('table', $types);
        $this->assertGreaterThanOrEqual(5, count($blocks));
    }

    public function test_each_block_has_id(): void
    {
        $blocks = $this->service->parse("# Titre\n\nParagraphe");
        foreach ($blocks as $block) {
            $this->assertArrayHasKey('id', $block);
            $this->assertNotEmpty($block['id']);
        }
    }
}
