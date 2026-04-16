<?php

namespace Tests\Feature\Journal;

use App\Mail\ReviewCompleted;
use App\Models\Review;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReviewFormTest extends TestCase
{
    use RefreshDatabase;

    private function makeAcceptedReview(): array
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        $editor = User::factory()->create(['email_verified_at' => now()]);
        $reviewer = User::factory()->create(['email_verified_at' => now()]);
        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Test article',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'under_peer_review',
            'submitted_at' => now(),
            'editor_id' => $editor->id,
        ]);
        $review = Review::create([
            'submission_id' => $submission->id,
            'reviewer_id' => $reviewer->id,
            'status' => Review::STATUS_ACCEPTED,
            'invited_at' => now()->subDays(3),
            'responded_at' => now()->subDays(2),
            'due_date' => now()->addDays(18),
        ]);
        return [$reviewer, $review, $editor];
    }

    public function test_reviewer_can_submit_evaluation(): void
    {
        Mail::fake();
        [$reviewer, $review] = $this->makeAcceptedReview();

        $this->actingAs($reviewer)
            ->post(route('review.form.store', $review), [
                'score_originality' => 4,
                'score_methodology' => 3,
                'score_clarity' => 5,
                'score_significance' => 4,
                'score_references' => 3,
                'comments_to_author' => str_repeat('Commentaire détaillé pour auteur. ', 5),
                'comments_to_editor' => 'Note confidentielle.',
                'recommendation' => 'minor_revision',
            ])
            ->assertRedirect();

        $fresh = $review->fresh();
        $this->assertSame(Review::STATUS_COMPLETED, $fresh->status);
        $this->assertNotNull($fresh->completed_at);
        $this->assertSame(4, $fresh->score_originality);
        $this->assertSame('minor_revision', $fresh->recommendation);
        Mail::assertQueued(ReviewCompleted::class);
    }

    public function test_other_user_cannot_access_review_form(): void
    {
        [, $review] = $this->makeAcceptedReview();
        $stranger = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($stranger)
            ->get(route('review.form', $review))
            ->assertForbidden();
    }

    public function test_cannot_submit_if_review_not_accepted(): void
    {
        [$reviewer, $review] = $this->makeAcceptedReview();
        $review->update(['status' => Review::STATUS_INVITED]);

        $this->actingAs($reviewer)
            ->get(route('review.form', $review))
            ->assertForbidden();
    }
}
