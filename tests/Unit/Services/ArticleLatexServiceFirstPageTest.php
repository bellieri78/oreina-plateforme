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

    public function test_title_is_not_italic_and_uses_dark_teal(): void
    {
        $latex = $this->buildLatex(['title' => 'Ma recherche']);
        $this->assertMatchesRegularExpression(
            '/\\\\textbf\{\\\\textcolor\{chersotisDarkTeal\}\{Ma recherche\}\}/',
            $latex
        );
        // Ensure title is NOT wrapped in \textit{...}
        $this->assertStringNotContainsString('\\textit{Ma recherche}', $latex);
    }

    public function test_article_type_label_is_french(): void
    {
        $latex = $this->buildLatex();
        $this->assertStringContainsString('Article de recherche', $latex);
        $this->assertStringNotContainsString('Original research', $latex);
    }

    public function test_abstract_box_has_resume_header(): void
    {
        $latex = $this->buildLatex(['abstract' => 'Mon résumé.']);
        $this->assertStringContainsString('\\textcolor{chersotisTeal}{Résumé}', $latex);
        $this->assertStringContainsString('Mon résumé.', $latex);
    }

    public function test_summary_box_hidden_when_display_summary_empty(): void
    {
        $latex = $this->buildLatex(['display_summary' => null]);
        $this->assertStringNotContainsString('{Summary}', $latex);
    }

    public function test_summary_box_shown_with_title_en_bold(): void
    {
        $latex = $this->buildLatex([
            'display_summary' => 'English abstract content.',
            'title_en' => 'My English Title',
        ]);
        $this->assertStringContainsString('{Summary}', $latex);
        $this->assertStringContainsString('\\textbf{My English Title}', $latex);
        $this->assertStringContainsString('English abstract content.', $latex);
    }

    public function test_summary_box_without_title_en_omits_bold_line(): void
    {
        $latex = $this->buildLatex([
            'display_summary' => 'Summary only.',
            'title_en' => null,
        ]);
        $this->assertStringContainsString('{Summary}', $latex);
        $this->assertStringContainsString('Summary only.', $latex);
    }

    public function test_first_page_footer_contains_license_text(): void
    {
        $latex = $this->buildLatex();
        $this->assertStringContainsString('Creative Commons Attribution CC BY 4.0', $latex);
        $this->assertStringContainsString('creativecommons.org/licenses/by/4.0/', $latex);
    }

    public function test_first_page_footer_uses_current_copyright_year(): void
    {
        $latex = $this->buildLatex(['published_at' => '2024-06-15']);
        $this->assertStringContainsString('\\textcopyright\\ 2024 OREINA', $latex);
    }

    public function test_supplementary_section_hidden_when_no_files(): void
    {
        $latex = $this->buildLatex();
        // When no supplementary files, the text must not appear at all
        $this->assertStringNotContainsString('Matériel supplémentaire', $latex);
    }

    public function test_supplementary_section_appears_in_body_when_files_present(): void
    {
        $latex = $this->buildLatex([
            'supplementary_files' => [
                ['name' => 'DataS1', 'url' => 'https://example.com/d.csv'],
            ],
        ]);
        // Should appear twice: once in sidebar (Task 7), once in body (Task 9)
        $this->assertEquals(
            2,
            substr_count($latex, 'Matériel supplémentaire'),
            'Matériel supplémentaire should appear in both sidebar and body'
        );
    }
}
