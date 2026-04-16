<?php

namespace Tests\Feature\Journal;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\SubmissionTransition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeAuthorAndSubmission(SubmissionStatus $status): array
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        $submission = Submission::create([
            'author_id' => $author->id,
            'title' => 'Mon article scientifique',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => $status->value,
            'submitted_at' => now()->subDays(5),
        ]);
        return [$author, $submission];
    }

    public function test_show_displays_6_step_timeline(): void
    {
        [$author, $submission] = $this->makeAuthorAndSubmission(SubmissionStatus::UnderPeerReview);

        $this->actingAs($author)
            ->get(route('journal.submissions.show', $submission))
            ->assertOk()
            ->assertSee('Soumis')
            ->assertSee('En évaluation', false)
            ->assertSee('Relecture')
            ->assertSee('Décision', false)
            ->assertSee('Maquettage')
            ->assertSee('Publié', false);
    }

    public function test_show_displays_activity_log_with_human_labels(): void
    {
        [$author, $submission] = $this->makeAuthorAndSubmission(SubmissionStatus::UnderPeerReview);

        SubmissionTransition::create([
            'submission_id' => $submission->id,
            'actor_user_id' => $author->id,
            'action' => SubmissionTransition::ACTION_STATUS_CHANGED,
            'from_status' => 'submitted',
            'to_status' => 'under_initial_review',
        ]);
        SubmissionTransition::create([
            'submission_id' => $submission->id,
            'actor_user_id' => $author->id,
            'action' => SubmissionTransition::ACTION_STATUS_CHANGED,
            'from_status' => 'under_initial_review',
            'to_status' => 'under_peer_review',
        ]);

        $this->actingAs($author)
            ->get(route('journal.submissions.show', $submission))
            ->assertOk()
            ->assertSee('Historique')
            ->assertSee('Votre manuscrit est en cours', false)
            ->assertSee('envoyé en relecture', false);
    }

    public function test_show_hides_internal_transitions(): void
    {
        [$author, $submission] = $this->makeAuthorAndSubmission(SubmissionStatus::UnderInitialReview);

        SubmissionTransition::create([
            'submission_id' => $submission->id,
            'actor_user_id' => $author->id,
            'action' => SubmissionTransition::ACTION_EDITOR_TAKEN,
            'target_user_id' => $author->id,
        ]);

        $response = $this->actingAs($author)
            ->get(route('journal.submissions.show', $submission))
            ->assertOk();

        $this->assertStringNotContainsString('editor_taken', $response->getContent());
    }

    public function test_show_displays_action_required_for_revision_requested(): void
    {
        [$author, $submission] = $this->makeAuthorAndSubmission(SubmissionStatus::RevisionRequested);

        $this->actingAs($author)
            ->get(route('journal.submissions.show', $submission))
            ->assertOk()
            ->assertSee('Action requise')
            ->assertSee('Soumettre ma révision', false)
            ->assertSee('Des compléments vous sont demandés', false);
    }

    public function test_show_displays_action_required_for_revision_after_review(): void
    {
        [$author, $submission] = $this->makeAuthorAndSubmission(SubmissionStatus::RevisionAfterReview);

        $this->actingAs($author)
            ->get(route('journal.submissions.show', $submission))
            ->assertOk()
            ->assertSee('Action requise')
            ->assertSee('Soumettre ma révision', false)
            ->assertSee('Une révision est demandée', false);
    }

    public function test_revision_button_visible_for_both_revision_statuses(): void
    {
        [$author1, $sub1] = $this->makeAuthorAndSubmission(SubmissionStatus::RevisionRequested);
        [$author2, $sub2] = $this->makeAuthorAndSubmission(SubmissionStatus::RevisionAfterReview);

        $this->actingAs($author1)
            ->get(route('journal.submissions.show', $sub1))
            ->assertOk()
            ->assertSee('Soumettre révision', false);

        $this->actingAs($author2)
            ->get(route('journal.submissions.show', $sub2))
            ->assertOk()
            ->assertSee('Soumettre révision', false);
    }

    public function test_index_shows_action_required_badge(): void
    {
        [$author, $submission] = $this->makeAuthorAndSubmission(SubmissionStatus::RevisionRequested);

        $this->actingAs($author)
            ->get(route('journal.submissions.index'))
            ->assertOk()
            ->assertSee('Action requise');
    }

    public function test_index_does_not_show_action_required_for_under_peer_review(): void
    {
        [$author, $submission] = $this->makeAuthorAndSubmission(SubmissionStatus::UnderPeerReview);

        $response = $this->actingAs($author)
            ->get(route('journal.submissions.index'))
            ->assertOk();

        $this->assertStringNotContainsString('Action requise', $response->getContent());
    }
}
