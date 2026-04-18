<?php

namespace Tests\Feature\Journal;

use App\Enums\SubmissionStatus;
use App\Mail\AuthorApproved;
use App\Mail\AuthorRequestedCorrections;
use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthorApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function makeSubmission(SubmissionStatus $status, User $author, ?User $editor = null, ?User $layoutEditor = null): Submission
    {
        return Submission::create([
            'author_id' => $author->id,
            'editor_id' => $editor?->id,
            'layout_editor_id' => $layoutEditor?->id,
            'title' => 'Test title for approval workflow',
            'abstract' => str_repeat('x', 150),
            'manuscript_file' => 'submissions/manuscripts/dummy.docx',
            'status' => $status,
            'submitted_at' => now(),
        ]);
    }

    public function test_author_can_approve_awaiting_submission(): void
    {
        Mail::fake();

        $author = User::factory()->create();
        $chief = User::factory()->create();
        $chief->capabilities()->create(['capability' => EditorialCapability::CHIEF_EDITOR, 'granted_at' => now()]);

        $submission = $this->makeSubmission(SubmissionStatus::AwaitingAuthorApproval, $author);

        $this->actingAs($author)
            ->post(route('journal.submissions.approve', $submission))
            ->assertRedirect(route('journal.submissions.show', $submission));

        $submission->refresh();
        $this->assertSame(SubmissionStatus::Published, $submission->status);
        $this->assertNotNull($submission->author_approved_at);

        Mail::assertQueued(AuthorApproved::class);
    }

    public function test_author_can_request_corrections(): void
    {
        Mail::fake();

        $author = User::factory()->create();
        $editor = User::factory()->create();
        $editor->capabilities()->create(['capability' => EditorialCapability::EDITOR, 'granted_at' => now()]);
        $layoutEditor = User::factory()->create();
        $layoutEditor->capabilities()->create(['capability' => EditorialCapability::LAYOUT_EDITOR, 'granted_at' => now()]);

        $submission = $this->makeSubmission(SubmissionStatus::AwaitingAuthorApproval, $author, $editor, $layoutEditor);

        $this->actingAs($author)
            ->post(route('journal.submissions.request-corrections', $submission), [
                'comment' => 'Veuillez corriger la figure 3, les légendes a et b sont inversées.',
            ])
            ->assertRedirect(route('journal.submissions.show', $submission));

        $submission->refresh();
        $this->assertSame(SubmissionStatus::InProduction, $submission->status);

        // One mail per recipient (editor + layout editor)
        Mail::assertQueued(AuthorRequestedCorrections::class, 2);
    }

    public function test_corrections_comment_is_required_and_min_20_chars(): void
    {
        $author = User::factory()->create();
        $submission = $this->makeSubmission(SubmissionStatus::AwaitingAuthorApproval, $author);

        $this->actingAs($author)
            ->post(route('journal.submissions.request-corrections', $submission), ['comment' => 'trop court'])
            ->assertSessionHasErrors(['comment']);
    }

    public function test_non_author_cannot_approve(): void
    {
        $author = User::factory()->create();
        $intruder = User::factory()->create();

        $submission = $this->makeSubmission(SubmissionStatus::AwaitingAuthorApproval, $author);

        $this->actingAs($intruder)
            ->post(route('journal.submissions.approve', $submission))
            ->assertForbidden();
    }

    public function test_approve_fails_if_not_awaiting_approval(): void
    {
        $author = User::factory()->create();
        $submission = $this->makeSubmission(SubmissionStatus::InProduction, $author);

        $this->actingAs($author)
            ->post(route('journal.submissions.approve', $submission))
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_non_author_cannot_request_corrections(): void
    {
        $author = User::factory()->create();
        $intruder = User::factory()->create();

        $submission = $this->makeSubmission(SubmissionStatus::AwaitingAuthorApproval, $author);

        $this->actingAs($intruder)
            ->post(route('journal.submissions.request-corrections', $submission), [
                'comment' => 'Un imposteur essaie de demander des corrections sans droits.',
            ])
            ->assertForbidden();
    }

    public function test_show_page_displays_approval_block_for_author(): void
    {
        $author = User::factory()->create();
        $submission = $this->makeSubmission(SubmissionStatus::AwaitingAuthorApproval, $author);

        $this->actingAs($author)
            ->get(route('journal.submissions.show', $submission))
            ->assertOk()
            ->assertSee('Votre article est prêt pour publication')
            ->assertSee('Approuver pour publication')
            ->assertSee('Signaler des corrections');
    }

    public function test_show_page_does_not_display_approval_block_for_other_statuses(): void
    {
        $author = User::factory()->create();
        $submission = $this->makeSubmission(SubmissionStatus::InProduction, $author);

        $this->actingAs($author)
            ->get(route('journal.submissions.show', $submission))
            ->assertOk()
            ->assertDontSee('Votre article est prêt pour publication');
    }

    public function test_show_page_forbids_non_author_access(): void
    {
        $author = User::factory()->create();
        $intruder = User::factory()->create();

        $submission = $this->makeSubmission(SubmissionStatus::AwaitingAuthorApproval, $author);

        $this->actingAs($intruder)
            ->get(route('journal.submissions.show', $submission))
            ->assertForbidden();
    }
}
