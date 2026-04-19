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
}
