<?php

namespace Tests\Unit\Models;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\SubmissionTransition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionPublicStatusTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(SubmissionStatus $status, User $author): Submission
    {
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'Test',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => $status,
        ]);
    }

    private function logTransition(Submission $submission, string $to, User $actor): void
    {
        $submission->transitions()->create([
            'action' => SubmissionTransition::ACTION_STATUS_CHANGED,
            'actor_user_id' => $actor->id,
            'from_status' => null,
            'to_status' => $to,
            'notes' => null,
        ]);
    }

    public function test_public_status_returns_direct_status_if_not_lepis_pending(): void
    {
        $author = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::UnderInitialReview, $author);

        $this->assertEquals(SubmissionStatus::UnderInitialReview, $sub->publicStatus());
    }

    public function test_public_status_returns_last_public_status_when_rejected_pending_lepis(): void
    {
        $author = User::factory()->create();
        $editor = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);

        $this->logTransition($sub, SubmissionStatus::UnderInitialReview->value, $editor);
        $this->logTransition($sub, SubmissionStatus::UnderPeerReview->value, $editor);
        $this->logTransition($sub, SubmissionStatus::RejectedPendingLepis->value, $editor);

        $this->assertEquals(SubmissionStatus::UnderPeerReview, $sub->publicStatus());
    }

    public function test_public_status_fallback_to_under_initial_review_if_no_transitions(): void
    {
        $author = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);

        $this->assertEquals(SubmissionStatus::UnderInitialReview, $sub->publicStatus());
    }

    public function test_public_status_ignores_transition_to_rejected_pending_lepis_itself(): void
    {
        $author = User::factory()->create();
        $editor = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);

        $this->logTransition($sub, SubmissionStatus::RejectedPendingLepis->value, $editor);

        $this->assertEquals(SubmissionStatus::UnderInitialReview, $sub->publicStatus());
    }
}
