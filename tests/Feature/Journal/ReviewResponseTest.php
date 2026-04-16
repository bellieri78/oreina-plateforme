<?php

namespace Tests\Feature\Journal;

use App\Mail\ReviewerAccepted;
use App\Mail\ReviewerDeclined;
use App\Models\Review;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ReviewResponseTest extends TestCase
{
    use RefreshDatabase;

    private function makeReviewInvitation(): Review
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        $editor = User::factory()->create(['email_verified_at' => now()]);
        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Test article',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => 'under_peer_review',
            'submitted_at' => now(),
            'editor_id' => $editor->id,
        ]);
        $reviewer = User::factory()->create(['email_verified_at' => now()]);
        return Review::create([
            'submission_id' => $submission->id,
            'reviewer_id' => $reviewer->id,
            'status' => Review::STATUS_INVITED,
            'invited_at' => now(),
        ]);
    }

    public function test_accept_via_signed_url_updates_status_and_sends_mail(): void
    {
        Mail::fake();
        $review = $this->makeReviewInvitation();
        $url = URL::signedRoute('journal.review.accept', ['review' => $review->id]);

        $this->post($url)->assertOk();

        $this->assertSame(Review::STATUS_ACCEPTED, $review->fresh()->status);
        $this->assertNotNull($review->fresh()->responded_at);
        $this->assertNotNull($review->fresh()->due_date);
        Mail::assertQueued(ReviewerAccepted::class);
    }

    public function test_decline_via_signed_url_updates_status_and_sends_mail(): void
    {
        Mail::fake();
        $review = $this->makeReviewInvitation();
        $url = URL::signedRoute('journal.review.decline', ['review' => $review->id]);

        $this->post($url)->assertOk();

        $this->assertSame(Review::STATUS_DECLINED, $review->fresh()->status);
        Mail::assertQueued(ReviewerDeclined::class);
    }

    public function test_already_responded_invitation_shows_message(): void
    {
        $review = $this->makeReviewInvitation();
        $review->update(['status' => Review::STATUS_ACCEPTED, 'responded_at' => now()]);

        $url = URL::signedRoute('journal.review.respond', ['review' => $review->id]);
        $this->get($url)->assertOk()->assertSee('déjà été traitée', false);
    }

    public function test_unsigned_url_is_rejected(): void
    {
        $review = $this->makeReviewInvitation();
        $this->get("/revue/relecture/{$review->id}/repondre")->assertForbidden();
    }
}
