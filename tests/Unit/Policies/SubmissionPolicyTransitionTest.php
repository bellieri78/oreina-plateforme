<?php

namespace Tests\Unit\Policies;

use App\Enums\SubmissionStatus;
use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\User;
use App\Policies\SubmissionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionPolicyTransitionTest extends TestCase
{
    use RefreshDatabase;

    private SubmissionPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new SubmissionPolicy();
    }

    private function makeSubmission(SubmissionStatus $status, ?int $editorId = null, ?int $layoutId = null): Submission
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'T',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => $status->value,
            'editor_id' => $editorId,
            'layout_editor_id' => $layoutId,
        ]);
    }

    public function test_author_can_transition_revision_requested_to_under_initial_review_as_author(): void
    {
        $submission = $this->makeSubmission(SubmissionStatus::RevisionRequested);
        $author = User::find($submission->author_id);

        $this->assertTrue($this->policy->transitionTo($author, $submission, SubmissionStatus::UnderInitialReview));
    }

    public function test_random_user_cannot_transition_submitted_to_under_initial_review(): void
    {
        $submission = $this->makeSubmission(SubmissionStatus::Submitted);
        $stranger = User::factory()->create(['email_verified_at' => now()]);

        $this->assertFalse($this->policy->transitionTo($stranger, $submission, SubmissionStatus::UnderInitialReview));
    }

    public function test_author_cannot_trigger_editorial_transition_on_submitted_submission(): void
    {
        $submission = $this->makeSubmission(SubmissionStatus::Submitted);
        $author = User::find($submission->author_id);

        // Author cannot promote their own submission past the editorial gate
        $this->assertFalse($this->policy->transitionTo($author, $submission, SubmissionStatus::UnderInitialReview));
        $this->assertFalse($this->policy->transitionTo($author, $submission, SubmissionStatus::Rejected));
    }

    public function test_editor_can_reject_from_under_initial_review(): void
    {
        $editor = User::factory()->create(['email_verified_at' => now()]);
        $editor->grantCapability(EditorialCapability::EDITOR);
        $submission = $this->makeSubmission(SubmissionStatus::UnderInitialReview, editorId: $editor->id);

        $this->assertTrue($this->policy->transitionTo($editor, $submission, SubmissionStatus::Rejected));
    }

    public function test_other_editor_cannot_reject(): void
    {
        $editor = User::factory()->create(['email_verified_at' => now()]);
        $editor->grantCapability(EditorialCapability::EDITOR);
        $otherEditor = User::factory()->create(['email_verified_at' => now()]);
        $otherEditor->grantCapability(EditorialCapability::EDITOR);

        $submission = $this->makeSubmission(SubmissionStatus::UnderInitialReview, editorId: $editor->id);

        $this->assertFalse($this->policy->transitionTo($otherEditor, $submission, SubmissionStatus::Rejected));
    }

    public function test_chief_editor_can_always_transition_editorial_states(): void
    {
        $chief = User::factory()->create(['email_verified_at' => now()]);
        $chief->grantCapability(EditorialCapability::CHIEF_EDITOR);
        $submission = $this->makeSubmission(SubmissionStatus::UnderPeerReview);

        $this->assertTrue($this->policy->transitionTo($chief, $submission, SubmissionStatus::Accepted));
        $this->assertTrue($this->policy->transitionTo($chief, $submission, SubmissionStatus::Rejected));
    }

    public function test_author_can_transition_revision_requested_to_under_initial_review(): void
    {
        $submission = $this->makeSubmission(SubmissionStatus::RevisionRequested);
        $author = User::find($submission->author_id);

        $this->assertTrue($this->policy->transitionTo($author, $submission, SubmissionStatus::UnderInitialReview));
    }

    public function test_editor_cannot_transition_revision_requested_to_under_initial_review(): void
    {
        $editor = User::factory()->create(['email_verified_at' => now()]);
        $editor->grantCapability(EditorialCapability::EDITOR);
        $submission = $this->makeSubmission(SubmissionStatus::RevisionRequested, editorId: $editor->id);

        $this->assertFalse($this->policy->transitionTo($editor, $submission, SubmissionStatus::UnderInitialReview));
    }

    public function test_submitted_to_under_initial_review_callable_by_assigned_editor_or_chief(): void
    {
        $editor = User::factory()->create(['email_verified_at' => now()]);
        $editor->grantCapability(EditorialCapability::EDITOR);
        $chief = User::factory()->create(['email_verified_at' => now()]);
        $chief->grantCapability(EditorialCapability::CHIEF_EDITOR);
        $stranger = User::factory()->create(['email_verified_at' => now()]);
        $submission = $this->makeSubmission(SubmissionStatus::Submitted, editorId: $editor->id);

        $this->assertTrue($this->policy->transitionTo($editor, $submission, SubmissionStatus::UnderInitialReview));
        $this->assertTrue($this->policy->transitionTo($chief, $submission, SubmissionStatus::UnderInitialReview));
        $this->assertFalse($this->policy->transitionTo($stranger, $submission, SubmissionStatus::UnderInitialReview));
    }

    public function test_layout_editor_can_publish(): void
    {
        $layout = User::factory()->create(['email_verified_at' => now()]);
        $layout->grantCapability(EditorialCapability::LAYOUT_EDITOR);
        $submission = $this->makeSubmission(SubmissionStatus::InProduction, layoutId: $layout->id);

        $this->assertTrue($this->policy->transitionTo($layout, $submission, SubmissionStatus::Published));
    }

    public function test_editor_cannot_publish(): void
    {
        $editor = User::factory()->create(['email_verified_at' => now()]);
        $editor->grantCapability(EditorialCapability::EDITOR);
        $submission = $this->makeSubmission(SubmissionStatus::InProduction, editorId: $editor->id);

        $this->assertFalse($this->policy->transitionTo($editor, $submission, SubmissionStatus::Published));
    }

    public function test_structurally_invalid_transition_always_false(): void
    {
        $chief = User::factory()->create(['email_verified_at' => now()]);
        $chief->grantCapability(EditorialCapability::CHIEF_EDITOR);
        $submission = $this->makeSubmission(SubmissionStatus::Submitted);

        $this->assertFalse($this->policy->transitionTo($chief, $submission, SubmissionStatus::Published));
    }
}
