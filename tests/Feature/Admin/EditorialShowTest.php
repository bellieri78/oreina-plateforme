<?php

namespace Tests\Feature\Admin;

use App\Enums\SubmissionStatus;
use App\Models\EditorialCapability;
use App\Models\Review;
use App\Models\Submission;
use App\Models\SubmissionTransition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialShowTest extends TestCase
{
    use RefreshDatabase;

    private function makeChief(): User
    {
        $u = User::factory()->create(['email_verified_at' => now(), 'role' => 'admin']);
        $u->grantCapability(EditorialCapability::CHIEF_EDITOR);
        return $u;
    }

    private function makeEditor(): User
    {
        $u = User::factory()->create(['email_verified_at' => now(), 'role' => 'editor']);
        $u->grantCapability(EditorialCapability::EDITOR);
        return $u;
    }

    private function makeReviewer(): User
    {
        $u = User::factory()->create(['email_verified_at' => now(), 'role' => 'editor']);
        $u->grantCapability(EditorialCapability::REVIEWER);
        return $u;
    }

    private function makeSubmission(SubmissionStatus $status, ?int $editorId = null): Submission
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'Test article editorial',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => $status->value,
            'editor_id' => $editorId,
            'submitted_at' => now(),
        ]);
    }

    public function test_invite_reviewer_creates_review_and_logs_transition(): void
    {
        $chief = $this->makeChief();
        $reviewer = $this->makeReviewer();
        $submission = $this->makeSubmission(SubmissionStatus::UnderPeerReview, editorId: $chief->id);

        $this->actingAs($chief)
            ->post(route('admin.journal.submissions.invite-reviewer', $submission), [
                'reviewer_id' => $reviewer->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'submission_id' => $submission->id,
            'reviewer_id' => $reviewer->id,
            'status' => Review::STATUS_INVITED,
        ]);

        $this->assertDatabaseHas('submission_transitions', [
            'submission_id' => $submission->id,
            'action' => SubmissionTransition::ACTION_REVIEWER_INVITED,
            'target_user_id' => $reviewer->id,
        ]);
    }

    public function test_invite_already_assigned_reviewer_fails(): void
    {
        $chief = $this->makeChief();
        $reviewer = $this->makeReviewer();
        $submission = $this->makeSubmission(SubmissionStatus::UnderPeerReview, editorId: $chief->id);

        Review::create([
            'submission_id' => $submission->id,
            'reviewer_id' => $reviewer->id,
            'status' => Review::STATUS_INVITED,
            'invited_at' => now(),
        ]);

        $this->actingAs($chief)
            ->post(route('admin.journal.submissions.invite-reviewer', $submission), [
                'reviewer_id' => $reviewer->id,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_reassign_editor_updates_editor_id(): void
    {
        $chief = $this->makeChief();
        $oldEditor = $this->makeEditor();
        $newEditor = $this->makeEditor();
        $submission = $this->makeSubmission(SubmissionStatus::UnderInitialReview, editorId: $oldEditor->id);

        $this->actingAs($chief)
            ->post(route('admin.journal.submissions.assign-editor', $submission), [
                'user_id' => $newEditor->id,
            ])
            ->assertRedirect();

        $this->assertSame($newEditor->id, $submission->fresh()->editor_id);
        $this->assertDatabaseHas('submission_transitions', [
            'submission_id' => $submission->id,
            'action' => SubmissionTransition::ACTION_EDITOR_ASSIGNED,
            'target_user_id' => $newEditor->id,
        ]);
    }

    public function test_show_page_displays_timeline_with_all_transitions(): void
    {
        $chief = $this->makeChief();
        $submission = $this->makeSubmission(SubmissionStatus::UnderInitialReview, editorId: $chief->id);

        SubmissionTransition::create([
            'submission_id' => $submission->id,
            'actor_user_id' => $chief->id,
            'action' => SubmissionTransition::ACTION_EDITOR_TAKEN,
            'target_user_id' => $chief->id,
        ]);
        SubmissionTransition::create([
            'submission_id' => $submission->id,
            'actor_user_id' => $chief->id,
            'action' => SubmissionTransition::ACTION_STATUS_CHANGED,
            'from_status' => 'submitted',
            'to_status' => 'under_initial_review',
        ]);

        $this->actingAs($chief)
            ->get(route('admin.submissions.show', $submission))
            ->assertOk()
            ->assertSee('Journal des actions')
            ->assertSee('Article pris en charge')
            ->assertSee('Changement de statut');
    }

    public function test_assign_layout_editor(): void
    {
        $chief = $this->makeChief();
        $layoutEditor = User::factory()->create(['email_verified_at' => now(), 'role' => 'editor']);
        $layoutEditor->grantCapability(EditorialCapability::LAYOUT_EDITOR);
        $submission = $this->makeSubmission(SubmissionStatus::Accepted, editorId: $chief->id);

        $this->actingAs($chief)
            ->post(route('admin.journal.submissions.assign-layout-editor', $submission), [
                'user_id' => $layoutEditor->id,
            ])
            ->assertRedirect();

        $this->assertSame($layoutEditor->id, $submission->fresh()->layout_editor_id);
    }
}
