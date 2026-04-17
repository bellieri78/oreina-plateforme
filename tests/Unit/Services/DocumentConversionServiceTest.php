<?php

namespace Tests\Unit\Services;

use App\Services\DocumentConversionService;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Tests\TestCase;

class DocumentConversionServiceTest extends TestCase
{
    private DocumentConversionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DocumentConversionService();
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
    // extractText tests
    // -------------------------------------------------------------------------

    public function test_extract_text_from_docx(): void
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Hello from docx');

        $path = $this->createDocx($phpWord);

        try {
            $text = $this->service->extractText($path);
            $this->assertStringContainsString('Hello from docx', $text);
        } finally {
            unlink($path);
        }
    }

    public function test_extract_text_from_odt(): void
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Hello from odt');

        $path = $this->createOdt($phpWord);

        try {
            $text = $this->service->extractText($path);
            $this->assertStringContainsString('Hello from odt', $text);
        } finally {
            unlink($path);
        }
    }

    public function test_extract_text_includes_table_content(): void
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $table = $section->addTable();
        $row = $table->addRow();
        $row->addCell()->addText('Cell A1');
        $row->addCell()->addText('Cell B1');
        $row2 = $table->addRow();
        $row2->addCell()->addText('Cell A2');
        $row2->addCell()->addText('Cell B2');

        $path = $this->createDocx($phpWord);

        try {
            $text = $this->service->extractText($path);
            $this->assertStringContainsString('Cell A1', $text);
            $this->assertStringContainsString('Cell B2', $text);
        } finally {
            unlink($path);
        }
    }

    public function test_extract_text_includes_list_items(): void
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addListItem('First item', 0);
        $section->addListItem('Second item', 0);

        $path = $this->createDocx($phpWord);

        try {
            $text = $this->service->extractText($path);
            $this->assertStringContainsString('First item', $text);
            $this->assertStringContainsString('Second item', $text);
        } finally {
            unlink($path);
        }
    }

    // -------------------------------------------------------------------------
    // toMarkdown / API tests
    // -------------------------------------------------------------------------

    public function test_throws_when_api_key_missing(): void
    {
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Some content');

        $path = $this->createDocx($phpWord);

        try {
            config(['services.anthropic.api_key' => null]);

            $this->expectException(\RuntimeException::class);
            $this->service->toMarkdown($path);
        } finally {
            unlink($path);
        }
    }

    public function test_throws_when_api_fails(): void
    {
        Http::fake([
            'https://api.anthropic.com/v1/messages' => Http::response(
                ['error' => ['message' => 'Unauthorized']],
                401
            ),
        ]);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Some content');

        $path = $this->createDocx($phpWord);

        try {
            config(['services.anthropic.api_key' => 'test-key']);

            $this->expectException(\RuntimeException::class);
            $this->service->toMarkdown($path);
        } finally {
            unlink($path);
        }
    }

    public function test_to_markdown_returns_string(): void
    {
        Http::fake([
            'https://api.anthropic.com/v1/messages' => Http::response([
                'content' => [
                    ['type' => 'text', 'text' => '# Titre\n\nContenu du document.'],
                ],
            ], 200),
        ]);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Contenu du document');

        $path = $this->createDocx($phpWord);

        try {
            config(['services.anthropic.api_key' => 'test-api-key']);

            $result = $this->service->toMarkdown($path);

            $this->assertIsString($result);
            $this->assertStringContainsString('Titre', $result);

            Http::assertSent(function ($request) {
                return $request->url() === 'https://api.anthropic.com/v1/messages'
                    && $request->header('x-api-key')[0] === 'test-api-key'
                    && $request->header('anthropic-version')[0] === '2023-06-01'
                    && str_contains($request->body(), 'claude-haiku')
                    && str_contains($request->body(), 'Contenu du document');
            });
        } finally {
            unlink($path);
        }
    }

    public function test_throws_when_document_empty(): void
    {
        $phpWord = new PhpWord();
        $phpWord->addSection(); // empty section, no text

        $path = $this->createDocx($phpWord);

        try {
            config(['services.anthropic.api_key' => 'test-api-key']);

            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessageMatches('/texte exploitable/i');
            $this->service->toMarkdown($path);
        } finally {
            unlink($path);
        }
    }
}
