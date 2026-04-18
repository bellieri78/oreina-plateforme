<?php

namespace Tests\Feature\Journal;

use App\Enums\SubmissionStatus;
use App\Mail\ArticleRedirectedToLepis;
use App\Mail\LepisQueueNotification;
use App\Mail\SubmissionDecision;
use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LepisQueueTest extends TestCase
{
    use RefreshDatabase;

    private function makeEditor(string $capability = EditorialCapability::EDITOR): User
    {
        $user = User::factory()->create();
        $user->grantCapability($capability);
        return $user;
    }

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function makeSubmission(SubmissionStatus $status, User $author, ?User $editor = null): Submission
    {
        return Submission::create([
            'author_id' => $author->id,
            'editor_id' => $editor?->id,
            'title' => 'Test Lepis',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => $status,
        ]);
    }

    public function test_rejection_with_lepis_checkbox_routes_to_rejected_pending_lepis(): void
    {
        Mail::fake();
        $editor = $this->makeEditor(EditorialCapability::EDITOR);
        $author = User::factory()->create();
        $admin = $this->makeAdmin(); // ensures at least one recipient so LepisQueueNotification is queued
        $sub = $this->makeSubmission(SubmissionStatus::UnderPeerReview, $author, $editor);

        $this->actingAs($editor)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => SubmissionStatus::Rejected->value,
                'notes' => 'Ce travail conviendrait mieux à Lepis.',
                'redirect_to_lepis' => '1',
            ]);

        $sub->refresh();
        $this->assertEquals(SubmissionStatus::RejectedPendingLepis, $sub->status);
        $this->assertTrue((bool) $sub->redirected_to_lepis);

        Mail::assertNotQueued(SubmissionDecision::class);
        Mail::assertQueued(LepisQueueNotification::class);
    }

    public function test_lepis_queue_notification_sent_to_admins_and_chief_editors(): void
    {
        Mail::fake();
        $editor = $this->makeEditor(EditorialCapability::EDITOR);
        $chief = $this->makeEditor(EditorialCapability::CHIEF_EDITOR);
        $admin = $this->makeAdmin();
        $author = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::UnderPeerReview, $author, $editor);

        $this->actingAs($editor)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => SubmissionStatus::Rejected->value,
                'notes' => 'Motif',
                'redirect_to_lepis' => '1',
            ]);

        Mail::assertQueued(LepisQueueNotification::class, fn ($m) => $m->hasTo($chief->email));
        Mail::assertQueued(LepisQueueNotification::class, fn ($m) => $m->hasTo($admin->email));
    }

    public function test_admin_can_access_lepis_queue_page(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.journal.lepis-queue'));

        $response->assertOk();
        $response->assertSee('File Lepis');
    }

    public function test_chief_editor_can_access_lepis_queue_page(): void
    {
        $chief = $this->makeEditor(EditorialCapability::CHIEF_EDITOR);

        $response = $this->actingAs($chief)->get(route('admin.journal.lepis-queue'));

        $response->assertOk();
    }

    public function test_simple_editor_cannot_access_lepis_queue_page(): void
    {
        $editor = $this->makeEditor(EditorialCapability::EDITOR);

        $response = $this->actingAs($editor)->get(route('admin.journal.lepis-queue'));

        $response->assertForbidden();
    }

    public function test_lepis_queue_lists_only_pending_submissions(): void
    {
        $admin = $this->makeAdmin();
        $author = User::factory()->create();

        $pending = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);
        $rejected = $this->makeSubmission(SubmissionStatus::Rejected, $author);
        $peer = $this->makeSubmission(SubmissionStatus::UnderPeerReview, $author);

        $response = $this->actingAs($admin)->get(route('admin.journal.lepis-queue'));

        $response->assertOk();
        $this->assertEquals(1, $response->viewData('submissions')->total());
    }

    public function test_transmit_to_lepis_sends_mail_and_sets_decision_timestamp(): void
    {
        Mail::fake();
        $admin = $this->makeAdmin();
        $author = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);

        $this->actingAs($admin)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => SubmissionStatus::RedirectedToLepis->value,
            ]);

        $sub->refresh();
        $this->assertEquals(SubmissionStatus::RedirectedToLepis, $sub->status);
        $this->assertNotNull($sub->lepis_decision_at);
        $this->assertEquals($admin->id, $sub->lepis_decided_by_user_id);

        Mail::assertQueued(ArticleRedirectedToLepis::class, fn ($m) => $m->hasTo($author->email));
        Mail::assertNotQueued(SubmissionDecision::class);
    }

    public function test_reject_from_lepis_queue_sends_submission_decision_mail(): void
    {
        Mail::fake();
        $admin = $this->makeAdmin();
        $author = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);

        $this->actingAs($admin)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => SubmissionStatus::Rejected->value,
                'notes' => 'Motifs de rejet détaillés',
            ]);

        $sub->refresh();
        $this->assertEquals(SubmissionStatus::Rejected, $sub->status);
        $this->assertNotNull($sub->lepis_decision_at);
        $this->assertEquals($admin->id, $sub->lepis_decided_by_user_id);

        Mail::assertQueued(SubmissionDecision::class, fn ($m) => $m->hasTo($author->email));
        Mail::assertNotQueued(ArticleRedirectedToLepis::class);
    }

    public function test_simple_editor_cannot_transmit_to_lepis(): void
    {
        $editor = $this->makeEditor(EditorialCapability::EDITOR);
        $author = User::factory()->create();
        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author);

        $response = $this->actingAs($editor)
            ->post(route('admin.journal.submissions.transition', $sub), [
                'target_status' => SubmissionStatus::RedirectedToLepis->value,
            ]);

        $response->assertForbidden();
        $sub->refresh();
        $this->assertEquals(SubmissionStatus::RejectedPendingLepis, $sub->status);
    }

    public function test_author_sees_public_status_not_lepis_pending_on_show_page(): void
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        $editor = $this->makeEditor(EditorialCapability::EDITOR);

        // Créer une soumission actuellement en RejectedPendingLepis avec un historique passant par UnderPeerReview
        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author, $editor);

        // Log manuellement les transitions pour simuler le passage par UnderPeerReview avant RejectedPendingLepis
        $sub->transitions()->create([
            'action' => 'status_changed',
            'actor_user_id' => $editor->id,
            'from_status' => 'submitted',
            'to_status' => 'under_peer_review',
            'notes' => null,
        ]);
        $sub->transitions()->create([
            'action' => 'status_changed',
            'actor_user_id' => $editor->id,
            'from_status' => 'under_peer_review',
            'to_status' => 'rejected_pending_lepis',
            'notes' => 'reco Lepis',
        ]);

        $response = $this->actingAs($author)->get(route('journal.submissions.show', $sub));

        $response->assertOk();
        $response->assertSee('En relecture');  // label de UnderPeerReview
        $response->assertDontSee('Rejet en attente Lepis');
        $response->assertDontSee('Transmis au bulletin Lepis');
    }

    public function test_author_dashboard_hides_lepis_pending_status(): void
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        $editor = $this->makeEditor(EditorialCapability::EDITOR);

        $sub = $this->makeSubmission(SubmissionStatus::RejectedPendingLepis, $author, $editor);

        $sub->transitions()->create([
            'action' => 'status_changed',
            'actor_user_id' => $editor->id,
            'from_status' => 'submitted',
            'to_status' => 'under_peer_review',
            'notes' => null,
        ]);
        $sub->transitions()->create([
            'action' => 'status_changed',
            'actor_user_id' => $editor->id,
            'from_status' => 'under_peer_review',
            'to_status' => 'rejected_pending_lepis',
            'notes' => 'reco Lepis',
        ]);

        $response = $this->actingAs($author)->get(route('member.dashboard'));

        $response->assertOk();
        $response->assertSee('En relecture');           // publicStatus() → UnderPeerReview label
        $response->assertDontSee('Rejet en attente Lepis');
        $response->assertDontSee('Inconnu');            // fuite partielle corrigée
        $response->assertDontSee('Transmis au bulletin Lepis');
    }
}
