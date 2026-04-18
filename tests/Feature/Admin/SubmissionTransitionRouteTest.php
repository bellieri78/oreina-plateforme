<?php

namespace Tests\Feature\Admin;

use App\Enums\SubmissionStatus;
use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionTransitionRouteTest extends TestCase
{
    use RefreshDatabase;

    private function makeEditor(): User
    {
        $u = User::factory()->create(['email_verified_at' => now(), 'role' => 'editor']);
        $u->grantCapability(EditorialCapability::EDITOR);
        return $u;
    }

    private function makeSubmission(SubmissionStatus $status, ?int $editorId = null): Submission
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'T',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => $status->value,
            'editor_id' => $editorId,
        ]);
    }

    public function test_editor_transitions_article_to_under_peer_review(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission(SubmissionStatus::UnderInitialReview, editorId: $editor->id);

        $this->actingAs($editor)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => 'under_peer_review',
                'notes' => 'OK recevable',
            ])
            ->assertRedirect();

        $this->assertSame('under_peer_review', $sub->fresh()->status->value);
        $this->assertDatabaseHas('submission_transitions', [
            'submission_id' => $sub->id,
            'from_status' => 'under_initial_review',
            'to_status' => 'under_peer_review',
            'notes' => 'OK recevable',
        ]);
    }

    public function test_reject_with_redirect_to_lepis_routes_to_pending_lepis_status(): void
    {
        // Depuis P1 #B : cocher "Recommander pour Lepis" envoie vers le
        // statut intermédiaire rejected_pending_lepis (invisible à l'auteur),
        // pas directement rejected. La décision finale (transmis à Lepis vs
        // rejeté définitivement) est prise par l'admin depuis /file-lepis.
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission(SubmissionStatus::UnderInitialReview, editorId: $editor->id);

        $this->actingAs($editor)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => 'rejected',
                'notes' => 'Hors scope Chersotis',
                'redirect_to_lepis' => '1',
            ])
            ->assertRedirect();

        $fresh = $sub->fresh();
        $this->assertSame('rejected_pending_lepis', $fresh->status->value);
        $this->assertTrue($fresh->redirected_to_lepis);
    }

    public function test_non_editor_gets_403(): void
    {
        $editor = $this->makeEditor();
        $stranger = User::factory()->create(['email_verified_at' => now(), 'role' => 'editor']);
        $stranger->grantCapability(EditorialCapability::EDITOR);
        $sub = $this->makeSubmission(SubmissionStatus::UnderInitialReview, editorId: $editor->id);

        $this->actingAs($stranger)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => 'rejected',
                'notes' => 'test',
            ])
            ->assertForbidden();
    }

    public function test_structurally_invalid_transition_403(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission(SubmissionStatus::Submitted, editorId: $editor->id);

        $this->actingAs($editor)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => 'published',
            ])
            ->assertForbidden();
    }

    public function test_invalid_target_status_rejected_by_validation(): void
    {
        $editor = $this->makeEditor();
        $sub = $this->makeSubmission(SubmissionStatus::UnderInitialReview, editorId: $editor->id);

        $this->actingAs($editor)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => 'not_a_real_status',
            ])
            ->assertSessionHasErrors('target_status');
    }

    private function makeSubmissionInProduction(User $author, User $editor): Submission
    {
        return Submission::create([
            'author_id'       => $author->id,
            'editor_id'       => $editor->id,
            'title'           => 'Titre test maquettage',
            'abstract'        => str_repeat('b', 120),
            'manuscript_file' => 'dummy.docx',
            'status'          => SubmissionStatus::InProduction->value,
        ]);
    }

    private function makeSubmissionAwaitingAuthorApproval(User $author, User $editor): Submission
    {
        return Submission::create([
            'author_id'       => $author->id,
            'editor_id'       => $editor->id,
            'title'           => 'Titre test approbation',
            'abstract'        => str_repeat('c', 120),
            'manuscript_file' => 'dummy.docx',
            'status'          => SubmissionStatus::AwaitingAuthorApproval->value,
        ]);
    }

    public function test_editor_can_request_author_approval_and_mail_is_sent(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $editor = $this->makeEditor();
        $author = User::factory()->create(['email_verified_at' => now()]);

        $submission = $this->makeSubmissionInProduction($author, $editor);

        $this->actingAs($editor)
            ->post(route('admin.journal.submissions.transition', $submission), [
                'target_status' => SubmissionStatus::AwaitingAuthorApproval->value,
            ])
            ->assertRedirect();

        $submission->refresh();
        $this->assertSame(SubmissionStatus::AwaitingAuthorApproval, $submission->status);
        $this->assertNotNull($submission->author_approval_requested_at);

        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\AuthorApprovalRequested::class);
    }

    public function test_author_approval_to_published_sends_notification_to_editorial_team(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $author = User::factory()->create(['email_verified_at' => now()]);
        $editor = $this->makeEditor();
        $chiefEditor = User::factory()->create(['email_verified_at' => now(), 'role' => 'editor']);
        $chiefEditor->grantCapability(EditorialCapability::CHIEF_EDITOR);

        $submission = $this->makeSubmissionAwaitingAuthorApproval($author, $editor);

        // Direct state machine call (bypasses HTTP/policy — we only verify the mail side effect)
        app(\App\Services\SubmissionStateMachine::class)->transition(
            $submission,
            SubmissionStatus::Published,
            $author,
        );

        $submission->refresh();
        $this->assertSame(SubmissionStatus::Published, $submission->status);
        $this->assertNotNull($submission->author_approved_at);

        \Illuminate\Support\Facades\Mail::assertQueued(\App\Mail\AuthorApproved::class, 2); // editor + chief editor
    }
}
