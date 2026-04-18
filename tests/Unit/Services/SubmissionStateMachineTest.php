<?php

namespace Tests\Unit\Services;

use App\Enums\SubmissionStatus;
use App\Exceptions\Editorial\IllegalTransitionException;
use App\Models\Submission;
use App\Models\SubmissionTransition;
use App\Models\User;
use App\Services\SubmissionStateMachine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionStateMachineTest extends TestCase
{
    use RefreshDatabase;

    private SubmissionStateMachine $sm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sm = app(SubmissionStateMachine::class);
    }

    private function makeSubmission(SubmissionStatus $status): Submission
    {
        $author = User::factory()->create(['email_verified_at' => now()]);
        return Submission::create([
            'author_id' => $author->id,
            'title' => 'T',
            'abstract' => str_repeat('a', 120),
            'manuscript_file' => 'placeholder.docx',
            'status' => $status->value,
        ]);
    }

    public static function validTransitions(): array
    {
        return [
            ['submitted', 'under_initial_review'],
            ['submitted', 'rejected'],
            ['under_initial_review', 'revision_requested'],
            ['under_initial_review', 'under_peer_review'],
            ['under_initial_review', 'rejected'],
            ['revision_requested', 'under_initial_review'],
            ['under_peer_review', 'revision_after_review'],
            ['under_peer_review', 'accepted'],
            ['under_peer_review', 'rejected'],
            ['revision_after_review', 'under_peer_review'],
            ['revision_after_review', 'accepted'],
            ['revision_after_review', 'rejected'],
            ['accepted', 'in_production'],
            ['in_production', 'published'],
        ];
    }

    /**
     * @dataProvider validTransitions
     */
    public function test_valid_transition_succeeds(string $from, string $to): void
    {
        $submission = $this->makeSubmission(SubmissionStatus::from($from));
        $actor = User::factory()->create(['email_verified_at' => now()]);

        $this->sm->transition($submission, SubmissionStatus::from($to), $actor, notes: 'test note');

        $this->assertSame($to, $submission->fresh()->status->value);
        $this->assertDatabaseHas('submission_transitions', [
            'submission_id' => $submission->id,
            'action' => SubmissionTransition::ACTION_STATUS_CHANGED,
            'actor_user_id' => $actor->id,
            'from_status' => $from,
            'to_status' => $to,
            'notes' => 'test note',
        ]);
    }

    public function test_invalid_transition_throws(): void
    {
        $submission = $this->makeSubmission(SubmissionStatus::Submitted);
        $actor = User::factory()->create(['email_verified_at' => now()]);

        $this->expectException(IllegalTransitionException::class);
        $this->sm->transition($submission, SubmissionStatus::Published, $actor);
    }

    public function test_published_is_terminal(): void
    {
        $submission = $this->makeSubmission(SubmissionStatus::Published);
        $actor = User::factory()->create(['email_verified_at' => now()]);

        $this->expectException(IllegalTransitionException::class);
        $this->sm->transition($submission, SubmissionStatus::Rejected, $actor);
    }

    public function test_rejected_is_terminal(): void
    {
        $submission = $this->makeSubmission(SubmissionStatus::Rejected);
        $actor = User::factory()->create(['email_verified_at' => now()]);

        $this->expectException(IllegalTransitionException::class);
        $this->sm->transition($submission, SubmissionStatus::Published, $actor);
    }

    public function test_can_transition_returns_bool(): void
    {
        $this->assertTrue($this->sm->canTransition(SubmissionStatus::Submitted, SubmissionStatus::UnderInitialReview));
        $this->assertFalse($this->sm->canTransition(SubmissionStatus::Submitted, SubmissionStatus::Published));
    }

    public function test_allowed_next_statuses_returns_enum_array(): void
    {
        $next = $this->sm->allowedNextStatuses(SubmissionStatus::UnderInitialReview);
        $values = array_map(fn($s) => $s->value, $next);

        $this->assertEqualsCanonicalizing(
            ['revision_requested', 'under_peer_review', 'rejected'],
            $values
        );
    }

    public function test_race_condition_throws_when_status_changed(): void
    {
        $submission = $this->makeSubmission(SubmissionStatus::Submitted);
        $actor = User::factory()->create(['email_verified_at' => now()]);

        // Simulate concurrency: modify status directly so the conditional UPDATE finds 0 rows
        Submission::where('id', $submission->id)->update(['status' => 'rejected']);

        $this->expectException(IllegalTransitionException::class);
        $this->sm->transition($submission, SubmissionStatus::UnderInitialReview, $actor);
    }
}
