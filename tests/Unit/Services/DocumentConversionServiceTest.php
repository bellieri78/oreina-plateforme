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
