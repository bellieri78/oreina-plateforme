<?php

namespace Tests\Unit\Services;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\User;
use App\Services\ArticleLatexService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleLatexServiceFirstPageTest extends TestCase
{
    use RefreshDatabase;

    private function buildLatex(array $overrides = []): string
    {
        $author = User::factory()->create([
            'name' => 'Jean Dupont',
            'email' => 'jean@example.com',
        ]);

        $submission = Submission::create(array_merge([
            'author_id' => $author->id,
            'title' => 'Étude phylogénétique',
            'abstract' => 'Un résumé court.',
            'manuscript_file' => 'placeholder.pdf',
            'keywords' => ['Lycaenidae', 'Alpes'],
            'status' => SubmissionStatus::Published,
            'published_at' => now(),
        ], $overrides));

        $service = app(ArticleLatexService::class);
        $reflection = new \ReflectionMethod($service, 'generateLatexContent');
        $reflection->setAccessible(true);

        return $reflection->invoke($service, $submission->fresh());
    }

    public function test_sidebar_contains_papillon_logo_include(): void
    {
        $latex = $this->buildLatex();
        $this->assertMatchesRegularExpression(
            '/\\\\includegraphics\[width=0\.7\\\\linewidth\]\{[^}]*oreina-papillon\.png\}/',
            $latex
        );
    }

    public function test_sidebar_contains_chersotis_wordmark(): void
    {
        $latex = $this->buildLatex();
        $this->assertStringContainsString(
            '\\textcolor{chersotisOrange}{Chersotis}',
            $latex
        );
    }

    public function test_sidebar_contains_harvard_citation_block(): void
    {
        $latex = $this->buildLatex();
        $this->assertStringContainsString('Citer cet article :', $latex);
        // Author surname "Dupont" must appear in the Harvard citation
        $this->assertStringContainsString('Dupont', $latex);
    }

    public function test_sidebar_contains_open_access_and_ccby_badges(): void
    {
        $latex = $this->buildLatex();
        $this->assertMatchesRegularExpression('/includegraphics\[height=24pt\]\{[^}]*open-access\.png\}/', $latex);
        $this->assertMatchesRegularExpression('/includegraphics\[height=24pt\]\{[^}]*cc-by-4\.0\.png\}/', $latex);
    }

    public function test_sidebar_omits_esm_when_no_supplementary_files(): void
    {
        $latex = $this->buildLatex();
        $this->assertStringNotContainsString('Matériel supplémentaire', $latex);
    }

    public function test_sidebar_shows_esm_when_supplementary_files_present(): void
    {
        $latex = $this->buildLatex([
            'supplementary_files' => [
                ['name' => 'Table S1', 'url' => 'https://example.com/s1.pdf'],
            ],
        ]);
        $this->assertStringContainsString('Matériel supplémentaire', $latex);
        $this->assertStringContainsString('Table S1', $latex);
    }

    public function test_sidebar_omits_orcid_when_show_orcid_false(): void
    {
        config(['journal.show_orcid' => false]);
        $latex = $this->buildLatex(['co_authors' => [['name' => 'X Y', 'orcid' => '0000-0001-0002-0003']]]);
        $this->assertStringNotContainsString('ORCID', $latex);
    }

    public function test_sidebar_shows_orcid_when_enabled(): void
    {
        config(['journal.show_orcid' => true]);
        $latex = $this->buildLatex(['co_authors' => [['name' => 'X Y', 'orcid' => '0000-0001-0002-0003']]]);
        $this->assertStringContainsString('0000-0001-0002-0003', $latex);
    }

    public function test_sidebar_contains_oreina_bottom_logo(): void
    {
        $latex = $this->buildLatex();
        $this->assertMatchesRegularExpression(
            '/includegraphics\[width=0\.6\\\\linewidth\]\{[^}]*oreina-noir-ligne\.png\}/',
            $latex
        );
    }
}
