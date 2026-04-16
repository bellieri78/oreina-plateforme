<?php

namespace Tests\Unit\Services;

use App\Enums\SubmissionStatus;
use App\Exceptions\Editorial\AlreadyAssignedException;
use App\Exceptions\Editorial\IneligibleUserException;
use App\Exceptions\Editorial\RoleConflictException;
use App\Models\EditorialCapability;
use App\Models\Review;
use App\Models\Submission;
use App\Models\SubmissionTransition;
use App\Models\User;
use App\Services\EditorialAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialAssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private EditorialAssignmentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(EditorialAssignmentService::class);
    }

    private function makeSubmission(): Submission
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'T',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => SubmissionStatus::Submitted,
        ]);
    }

    private function makeEditor(): User
    {
        $u = User::factory()->create(['email_verified_at' => now()]);
        $u->grantCapability(EditorialCapability::EDITOR);
        return $u;
    }

    private function makeChief(): User
    {
        $u = User::factory()->create(['email_verified_at' => now()]);
        $u->grantCapability(EditorialCapability::CHIEF_EDITOR);
        return $u;
    }

    public function test_assign_editor_success(): void
    {
        $submission = $this->makeSubmission();
        $editor = $this->makeEditor();
        $chief = $this->makeChief();

        $this->service->assignEditor($submission, $editor, $chief);

        $this->assertSame($editor->id, $submission->fresh()->editor_id);
        $this->assertDatabaseHas('submission_transitions', [
            'submission_id' => $submission->id,
            'action' => SubmissionTransition::ACTION_EDITOR_ASSIGNED,
            'actor_user_id' => $chief->id,
            'target_user_id' => $editor->id,
        ]);
    }

    public function test_assign_editor_fails_for_ineligible_user(): void
    {
        $submission = $this->makeSubmission();
        $notEditor = User::factory()->create(['email_verified_at' => now()]);
        $chief = $this->makeChief();

        $this->expectException(IneligibleUserException::class);
        $this->service->assignEditor($submission, $notEditor, $chief);
    }

    public function test_assign_editor_fails_if_target_is_already_reviewer(): void
    {
        $submission = $this->makeSubmission();
        $user = $this->makeEditor();
        $user->grantCapability(EditorialCapability::REVIEWER);

        Review::create([
            'submission_id' => $submission->id,
            'reviewer_id' => $user->id,
            'status' => Review::STATUS_INVITED,
            'invited_at' => now(),
        ]);

        $chief = $this->makeChief();

        $this->expectException(RoleConflictException::class);
        $this->service->assignEditor($submission, $user, $chief);
    }

    public function test_assign_editor_override_bypasses_role_conflict(): void
    {
        $submission = $this->makeSubmission();
        $user = $this->makeEditor();
        $user->grantCapability(EditorialCapability::REVIEWER);

        Review::create([
            'submission_id' => $submission->id,
            'reviewer_id' => $user->id,
            'status' => Review::STATUS_INVITED,
            'invited_at' => now(),
        ]);

        $chief = $this->makeChief();
        $this->service->assignEditor($submission, $user, $chief, override: true);

        $this->assertSame($user->id, $submission->fresh()->editor_id);
        $this->assertDatabaseHas('submission_transitions', [
            'submission_id' => $submission->id,
            'action' => SubmissionTransition::ACTION_EDITOR_ASSIGNED,
            'notes' => 'Override: séparation des rôles forcée',
        ]);
    }

    public function test_take_editor_success_when_unassigned(): void
    {
        $submission = $this->makeSubmission();
        $editor = $this->makeEditor();

        $this->service->takeEditor($submission, $editor);

        $this->assertSame($editor->id, $submission->fresh()->editor_id);
        $this->assertDatabaseHas('submission_transitions', [
            'submission_id' => $submission->id,
            'action' => SubmissionTransition::ACTION_EDITOR_TAKEN,
            'actor_user_id' => $editor->id,
            'target_user_id' => $editor->id,
        ]);
    }

    public function test_take_editor_fails_if_already_assigned(): void
    {
        $submission = $this->makeSubmission();
        $first = $this->makeEditor();
        $second = $this->makeEditor();

        $this->service->takeEditor($submission, $first);

        $this->expectException(AlreadyAssignedException::class);
        $this->service->takeEditor($submission, $second);
    }

    public function test_assign_reviewer_fails_if_target_is_editor_of_article(): void
    {
        $submission = $this->makeSubmission();
        $editor = $this->makeEditor();
        $editor->grantCapability(EditorialCapability::REVIEWER);
        $submission->update(['editor_id' => $editor->id]);

        $chief = $this->makeChief();

        $this->expectException(RoleConflictException::class);
        $this->service->assignReviewer($submission, $editor, $chief);
    }

    public function test_assign_reviewer_override_bypasses_editor_conflict(): void
    {
        $submission = $this->makeSubmission();
        $editor = $this->makeEditor();
        $editor->grantCapability(EditorialCapability::REVIEWER);
        $submission->update(['editor_id' => $editor->id]);

        $chief = $this->makeChief();
        $this->service->assignReviewer($submission, $editor, $chief, override: true);

        $this->assertDatabaseHas('reviews', [
            'submission_id' => $submission->id,
            'reviewer_id' => $editor->id,
            'status' => Review::STATUS_INVITED,
        ]);
    }

    public function test_assign_reviewer_fails_for_ineligible_user(): void
    {
        $submission = $this->makeSubmission();
        $random = User::factory()->create(['email_verified_at' => now()]);
        $chief = $this->makeChief();

        $this->expectException(IneligibleUserException::class);
        $this->service->assignReviewer($submission, $random, $chief);
    }

    public function test_assign_layout_editor_success(): void
    {
        $submission = $this->makeSubmission();
        $submission->update(['status' => SubmissionStatus::Accepted]);

        $layout = User::factory()->create(['email_verified_at' => now()]);
        $layout->grantCapability(EditorialCapability::LAYOUT_EDITOR);
        $chief = $this->makeChief();

        $this->service->assignLayoutEditor($submission, $layout, $chief);

        $this->assertSame($layout->id, $submission->fresh()->layout_editor_id);
    }

    public function test_take_editor_auto_transitions_submitted_to_under_initial_review(): void
    {
        $submission = $this->makeSubmission();
        $editor = $this->makeEditor();

        $this->service->takeEditor($submission, $editor);

        $this->assertSame('under_initial_review', $submission->fresh()->status->value);
        $this->assertDatabaseHas('submission_transitions', [
            'submission_id' => $submission->id,
            'action' => \App\Models\SubmissionTransition::ACTION_STATUS_CHANGED,
            'from_status' => 'submitted',
            'to_status' => 'under_initial_review',
        ]);
    }

    public function test_take_editor_does_not_transition_if_not_submitted(): void
    {
        $submission = $this->makeSubmission();
        \App\Models\Submission::where('id', $submission->id)->update(['status' => 'under_initial_review']);
        $submission->refresh();
        $editor = $this->makeEditor();

        $this->service->takeEditor($submission, $editor);

        $this->assertSame('under_initial_review', $submission->fresh()->status->value);
        $this->assertDatabaseMissing('submission_transitions', [
            'submission_id' => $submission->id,
            'action' => \App\Models\SubmissionTransition::ACTION_STATUS_CHANGED,
        ]);
    }

    public function test_assign_editor_auto_transitions_submitted(): void
    {
        $submission = $this->makeSubmission();
        $editor = $this->makeEditor();
        $chief = $this->makeChief();

        $this->service->assignEditor($submission, $editor, $chief);

        $this->assertSame('under_initial_review', $submission->fresh()->status->value);
    }
}
