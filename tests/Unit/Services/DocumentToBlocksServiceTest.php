<?php

namespace Tests\Unit\Services;

use App\Services\DocumentToBlocksService;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PHPUnit\Framework\TestCase;

class DocumentToBlocksServiceTest extends TestCase
{
    private DocumentToBlocksService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DocumentToBlocksService();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Create a temporary .docx file from a PhpWord document and return its path.
     */
    private function createDocx(PhpWord $phpWord): string
    {
        $path = tempnam(sys_get_temp_dir(), 'docx_') . '.docx';
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($path);

        return $path;
    }

    /**
     * Create a temporary .odt file from a PhpWord document and return its path.
     */
    private function createOdt(PhpWord $phpWord): string
    {
        $path = tempnam(sys_get_temp_dir(), 'odt_') . '.odt';
        $writer = IOFactory::createWriter($phpWord, 'ODText');
        $writer->save($path);

        return $path;
    }

    // -------------------------------------------------------------------------
    // Tests
    // -------------------------------------------------------------------------

    public function test_heading_by_style(): void
    {
        $phpWord = new PhpWord();
        // Register heading styles so Title elements survive write/read round-trip
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 16]);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14]);
        $phpWord->addTitleStyle(3, ['bold' => true, 'size' => 12]);
        $section = $phpWord->addSection();
        $section->addTitle('Premier titre', 1);
        $section->addTitle('Deuxième titre', 2);
        $section->addTitle('Troisième titre', 3);

        $path = $this->createDocx($phpWord);
        $blocks = $this->service->parseFile($path);
        unlink($path);

        $this->assertCount(3, $blocks);
        $this->assertSame('heading', $blocks[0]['type']);
        $this->assertSame('1', $blocks[0]['level']);
        $this->assertStringContainsString('Premier titre', $blocks[0]['content']);
        $this->assertSame('2', $blocks[1]['level']);
        $this->assertSame('3', $blocks[2]['level']);
    }

    public function test_heading_by_heuristic(): void
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // >=16pt → H1
        $run1 = $section->addTextRun();
        $run1->addText('Gros titre', ['bold' => true, 'size' => 18]);

        // >=14pt → H2
        $run2 = $section->addTextRun();
        $run2->addText('Titre moyen', ['bold' => true, 'size' => 14]);

        // bold, smaller → H3
        $run3 = $section->addTextRun();
        $run3->addText('Petit titre', ['bold' => true, 'size' => 12]);

        $path = $this->createDocx($phpWord);
        $blocks = $this->service->parseFile($path);
        unlink($path);

        $headings = array_filter($blocks, fn($b) => $b['type'] === 'heading');
        $headings = array_values($headings);

        $this->assertCount(3, $headings);
        $this->assertSame('1', $headings[0]['level']);
        $this->assertSame('2', $headings[1]['level']);
        $this->assertSame('3', $headings[2]['level']);
    }

    public function test_paragraph_with_inline_formatting(): void
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $run = $section->addTextRun();
        $run->addText('Ceci est ');
        $run->addText('gras', ['bold' => true]);
        $run->addText(' et ');
        $run->addText('italique', ['italic' => true]);
        $run->addText('.');

        $path = $this->createDocx($phpWord);
        $blocks = $this->service->parseFile($path);
        unlink($path);

        $this->assertCount(1, $blocks);
        $this->assertSame('paragraph', $blocks[0]['type']);
        $this->assertStringContainsString('<strong>gras</strong>', $blocks[0]['content']);
        $this->assertStringContainsString('<em>italique</em>', $blocks[0]['content']);
    }

    public function test_table(): void
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $table = $section->addTable();

        // Header row
        $row = $table->addRow();
        $row->addCell(2000)->addText('Espèce');
        $row->addCell(2000)->addText('Localité');

        // Data row 1
        $row = $table->addRow();
        $row->addCell(2000)->addText('P. apollo');
        $row->addCell(2000)->addText('Pyrénées');

        // Data row 2
        $row = $table->addRow();
        $row->addCell(2000)->addText('C. palaeno');
        $row->addCell(2000)->addText('Vosges');

        $path = $this->createDocx($phpWord);
        $blocks = $this->service->parseFile($path);
        unlink($path);

        $this->assertCount(1, $blocks);
        $this->assertSame('table', $blocks[0]['type']);
        $this->assertCount(2, $blocks[0]['headers']);
        $this->assertSame('Espèce', $blocks[0]['headers'][0]);
        $this->assertSame('Localité', $blocks[0]['headers'][1]);
        $this->assertCount(2, $blocks[0]['rows']);
        $this->assertSame('P. apollo', $blocks[0]['rows'][0][0]);
        $this->assertSame('Pyrénées', $blocks[0]['rows'][0][1]);
    }

    public function test_list_unordered(): void
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addListItem('Premier élément', 0);
        $section->addListItem('Deuxième élément', 0);
        $section->addListItem('Troisième', 0);

        $path = $this->createDocx($phpWord);
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
        $phpWord = new PhpWord();
        $phpWord->addNumberingStyle(
            'numStyle1',
            ['type' => 'multilevel', 'levels' => [
                ['start' => 1, 'format' => 'decimal', 'restart' => 1,
                    'suffix' => 'space', 'text' => '%1.', 'alignment' => 'left'],
            ]]
        );
        $section = $phpWord->addSection();
        $section->addListItem('Un', 0, null, 'numStyle1');
        $section->addListItem('Deux', 0, null, 'numStyle1');
        $section->addListItem('Trois', 0, null, 'numStyle1');

        $path = $this->createDocx($phpWord);
        $blocks = $this->service->parseFile($path);
        unlink($path);

        $this->assertCount(1, $blocks);
        $this->assertSame('list', $blocks[0]['type']);
        $this->assertSame('ordered', $blocks[0]['listType']);
        $this->assertCount(3, $blocks[0]['items']);
    }

    public function test_empty_paragraphs_skipped(): void
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addTextBreak(3);
        $section->addText('Contenu réel');

        $path = $this->createDocx($phpWord);
        $blocks = $this->service->parseFile($path);
        unlink($path);

        foreach ($blocks as $block) {
            $this->assertNotSame('', trim($block['content'] ?? ''));
        }
        $this->assertGreaterThanOrEqual(1, count($blocks));
    }

    public function test_each_block_has_id(): void
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addTitle('Titre', 1);
        $section->addText('Paragraphe');

        $path = $this->createDocx($phpWord);
        $blocks = $this->service->parseFile($path);
        unlink($path);

        $ids = [];
        foreach ($blocks as $block) {
            $this->assertArrayHasKey('id', $block);
            $this->assertStringStartsWith('block-doc-', $block['id']);
            $ids[] = $block['id'];
        }

        // All IDs must be unique
        $this->assertCount(count($blocks), array_unique($ids));
    }

    public function test_odt_format(): void
    {
        $phpWord = new PhpWord();
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 16]);
        $section = $phpWord->addSection();
        $section->addTitle('Titre ODT', 1);
        $section->addText('Paragraphe ODT');

        $path = $this->createOdt($phpWord);
        $blocks = $this->service->parseFile($path);
        unlink($path);

        $this->assertGreaterThanOrEqual(1, count($blocks));
        $types = array_column($blocks, 'type');
        $this->assertContains('heading', $types);
    }

    public function test_mixed_document(): void
    {
        $phpWord = new PhpWord();
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 16]);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14]);
        $section = $phpWord->addSection();

        $section->addTitle('Introduction', 1);
        $section->addText("Paragraphe d'intro.");
        $section->addTitle('Méthode', 2);
        $section->addListItem('Point 1', 0);
        $section->addListItem('Point 2', 0);

        $table = $section->addTable();
        $row = $table->addRow();
        $row->addCell(2000)->addText('Col1');
        $row->addCell(2000)->addText('Col2');
        $row = $table->addRow();
        $row->addCell(2000)->addText('A');
        $row->addCell(2000)->addText('B');

        $path = $this->createDocx($phpWord);
        $blocks = $this->service->parseFile($path);
        unlink($path);

        $types = array_column($blocks, 'type');
        $this->assertContains('heading', $types);
        $this->assertContains('paragraph', $types);
        $this->assertContains('list', $types);
        $this->assertContains('table', $types);
        $this->assertGreaterThanOrEqual(5, count($blocks));
    }
}
