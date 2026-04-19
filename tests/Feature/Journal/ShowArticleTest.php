<?php

namespace Tests\Feature\Journal;

use App\Jobs\SyncCrossrefCitationsJob;
use App\Models\ArticleEvent;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ShowArticleTest extends TestCase
{
    use RefreshDatabase;

    private function makePublished(array $attrs = []): Submission
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        return Submission::create(array_merge([
            'author_id' => $author->id,
            'title' => 'Published article',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'published',
            'published_at' => now(),
            'submitted_at' => now(),
        ], $attrs));
    }

    public function test_show_article_records_view_event(): void
    {
        Queue::fake();
        $submission = $this->makePublished();

        $this->get(route('journal.articles.show', $submission))->assertOk();

        $this->assertDatabaseHas('article_events', [
            'submission_id' => $submission->id,
            'event_type' => ArticleEvent::TYPE_VIEW,
        ]);
    }

    public function test_show_article_dispatches_crossref_job_when_stale(): void
    {
        Queue::fake();
        $submission = $this->makePublished([
            'doi' => '10.24349/chersotis.2026.0001',
            'citation_synced_at' => now()->subDays(10),
        ]);

        $this->get(route('journal.articles.show', $submission))->assertOk();

        Queue::assertPushed(SyncCrossrefCitationsJob::class, fn ($j) => $j->submissionId === $submission->id);
    }

    public function test_show_article_does_not_dispatch_job_when_fresh(): void
    {
        Queue::fake();
        $submission = $this->makePublished([
            'doi' => '10.24349/chersotis.2026.0001',
            'citation_synced_at' => now()->subDays(2),
        ]);

        $this->get(route('journal.articles.show', $submission))->assertOk();

        Queue::assertNotPushed(SyncCrossrefCitationsJob::class);
    }

    public function test_show_article_renders_sidebar_with_metrics(): void
    {
        \Illuminate\Support\Facades\Queue::fake();
        $submission = $this->makePublished(['doi' => '10.24349/chersotis.2026.0001']);

        $response = $this->get(route('journal.articles.show', $submission));

        $response->assertOk()
            ->assertSee('id="article-sidebar"', false)
            ->assertSee('data-metric="views"', false)
            ->assertSee('data-metric="pdf_downloads"', false)
            ->assertSee('data-metric="shares"', false)
            ->assertSee('data-metric="citations"', false);
    }

    public function test_show_article_renders_toc_from_h2_blocks(): void
    {
        \Illuminate\Support\Facades\Queue::fake();
        $submission = $this->makePublished([
            'content_blocks' => [
                ['type' => 'heading', 'level' => 'h2', 'content' => 'Introduction'],
                ['type' => 'paragraph', 'content' => 'Some text.'],
                ['type' => 'heading', 'level' => 'h2', 'content' => 'Méthodes'],
            ],
        ]);

        $response = $this->get(route('journal.articles.show', $submission));

        $response->assertOk()
            ->assertSee('1. Introduction')
            ->assertSee('2. Méthodes')
            ->assertSee('#section-1', false)
            ->assertSee('#section-2', false);
    }

    public function test_show_article_without_doi_hides_citation_metric(): void
    {
        \Illuminate\Support\Facades\Queue::fake();
        $submission = $this->makePublished(['doi' => null]);

        $response = $this->get(route('journal.articles.show', $submission));

        $response->assertOk()->assertDontSee('data-metric="citations"', false);
    }
}
