<?php

namespace Tests\Feature\Journal;

use App\Models\ArticleEvent;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackShareTest extends TestCase
{
    use RefreshDatabase;

    private function makePublished(): Submission
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'Published article',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'published',
            'published_at' => now(),
            'submitted_at' => now(),
        ]);
    }

    public function test_track_share_records_event_with_network(): void
    {
        $submission = $this->makePublished();

        $this->post(route('journal.articles.share', $submission), ['network' => 'twitter'])
            ->assertNoContent();

        $this->assertDatabaseHas('article_events', [
            'submission_id' => $submission->id,
            'event_type' => ArticleEvent::TYPE_SHARE,
            'network' => 'twitter',
        ]);
    }

    public function test_track_share_rejects_invalid_network(): void
    {
        $submission = $this->makePublished();

        $this->post(route('journal.articles.share', $submission), ['network' => 'myspace'])
            ->assertStatus(422);

        $this->assertDatabaseCount('article_events', 0);
    }

    public function test_track_share_requires_published_article(): void
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        $draft = Submission::create([
            'author_id' => $author->id,
            'title' => 'Draft',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->post(route('journal.articles.share', $draft), ['network' => 'twitter'])
            ->assertNotFound();
    }
}
