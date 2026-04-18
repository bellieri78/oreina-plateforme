<?php

namespace Tests\Unit\Services;

use App\Enums\SubmissionStatus;
use App\Services\SubmissionStateMachine;
use App\Services\SubmissionTransitionLogger;
use Tests\TestCase;

class SubmissionStateMachineLepisTest extends TestCase
{
    private SubmissionStateMachine $sm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sm = new SubmissionStateMachine(app(SubmissionTransitionLogger::class));
    }

    public function test_under_initial_review_to_rejected_pending_lepis_allowed(): void
    {
        $this->assertTrue($this->sm->canTransition(
            SubmissionStatus::UnderInitialReview,
            SubmissionStatus::RejectedPendingLepis
        ));
    }

    public function test_under_peer_review_to_rejected_pending_lepis_allowed(): void
    {
        $this->assertTrue($this->sm->canTransition(
            SubmissionStatus::UnderPeerReview,
            SubmissionStatus::RejectedPendingLepis
        ));
    }

    public function test_revision_after_review_to_rejected_pending_lepis_allowed(): void
    {
        $this->assertTrue($this->sm->canTransition(
            SubmissionStatus::RevisionAfterReview,
            SubmissionStatus::RejectedPendingLepis
        ));
    }

    public function test_rejected_pending_lepis_to_redirected_to_lepis_allowed(): void
    {
        $this->assertTrue($this->sm->canTransition(
            SubmissionStatus::RejectedPendingLepis,
            SubmissionStatus::RedirectedToLepis
        ));
    }

    public function test_rejected_pending_lepis_to_rejected_allowed(): void
    {
        $this->assertTrue($this->sm->canTransition(
            SubmissionStatus::RejectedPendingLepis,
            SubmissionStatus::Rejected
        ));
    }

    public function test_rejected_pending_lepis_to_accepted_forbidden(): void
    {
        $this->assertFalse($this->sm->canTransition(
            SubmissionStatus::RejectedPendingLepis,
            SubmissionStatus::Accepted
        ));
    }

    public function test_redirected_to_lepis_is_terminal(): void
    {
        $this->assertFalse($this->sm->canTransition(
            SubmissionStatus::RedirectedToLepis,
            SubmissionStatus::Rejected
        ));
        $this->assertFalse($this->sm->canTransition(
            SubmissionStatus::RedirectedToLepis,
            SubmissionStatus::Published
        ));
    }

    public function test_submitted_to_rejected_pending_lepis_forbidden(): void
    {
        $this->assertFalse($this->sm->canTransition(
            SubmissionStatus::Submitted,
            SubmissionStatus::RejectedPendingLepis
        ));
    }
}
