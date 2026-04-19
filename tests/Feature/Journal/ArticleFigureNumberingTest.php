<?php

namespace Tests\Feature\Journal;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleFigureNumberingTest extends TestCase
{
    use RefreshDatabase;

    private function publishedSubmissionWithBlocks(array $blocks): Submission
    {
        $author = User::factory()->create();
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'Article test numérotation',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => SubmissionStatus::Published,
            'published_at' => now(),
            'submitted_at' => now()->subMonth(),
            'content_blocks' => $blocks,
        ]);
    }

    public function test_figures_are_numbered_by_image_count_only_not_global_block_index(): void
    {
        // Intercalation : paragraph, image, paragraph, paragraph, image, heading, image
        $sub = $this->publishedSubmissionWithBlocks([
            ['type' => 'paragraph', 'content' => 'Intro'],
            ['type' => 'image', 'url' => '/img/a.jpg', 'caption' => 'Spécimen A'],
            ['type' => 'paragraph', 'content' => 'Texte intermédiaire'],
            ['type' => 'paragraph', 'content' => 'Autre paragraphe'],
            ['type' => 'image', 'url' => '/img/b.jpg', 'caption' => 'Carte de distribution'],
            ['type' => 'heading', 'level' => 'h2', 'content' => 'Discussion'],
            ['type' => 'image', 'url' => '/img/c.jpg', 'caption' => 'Comparaison'],
        ]);

        $response = $this->get(route('journal.articles.show', $sub));

        $response->assertOk();
        // Les 3 figures doivent être numérotées 1, 2, 3 indépendamment de leur
        // position globale (qui serait 2, 5, 7 si on utilisait $blockIndex+1)
        $response->assertSeeInOrder([
            'Figure 1.', 'Spécimen A',
            'Figure 2.', 'Carte de distribution',
            'Figure 3.', 'Comparaison',
        ]);
    }

    public function test_tables_are_numbered_separately_from_figures(): void
    {
        $sub = $this->publishedSubmissionWithBlocks([
            ['type' => 'image', 'url' => '/img/a.jpg', 'caption' => 'Photo'],
            ['type' => 'table', 'data' => [['H1', 'H2'], ['a', 'b']], 'caption' => 'Données brutes'],
            ['type' => 'image', 'url' => '/img/b.jpg', 'caption' => 'Schéma'],
            ['type' => 'table', 'data' => [['X', 'Y'], ['1', '2']], 'caption' => 'Résultats'],
        ]);

        $response = $this->get(route('journal.articles.show', $sub));

        $response->assertOk();
        // Les figures comptent 1, 2 et les tableaux comptent 1, 2 séparément
        $response->assertSeeInOrder([
            'Figure 1.', 'Photo',
            'Tableau 1.', 'Données brutes',
            'Figure 2.', 'Schéma',
            'Tableau 2.', 'Résultats',
        ]);
    }

    public function test_figure_without_caption_still_gets_number(): void
    {
        $sub = $this->publishedSubmissionWithBlocks([
            ['type' => 'image', 'url' => '/img/a.jpg', 'caption' => ''],
            ['type' => 'image', 'url' => '/img/b.jpg', 'caption' => 'Avec légende'],
        ]);

        $response = $this->get(route('journal.articles.show', $sub));

        $response->assertOk();
        $response->assertSee('Figure 1.');
        $response->assertSeeInOrder(['Figure 2.', 'Avec légende']);
    }
}
