<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Exceptions\Editorial\IllegalTransitionException;
use App\Mail\AuthorApprovalRequested;
use App\Mail\AuthorApproved;
use App\Mail\SubmissionDecision;
use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\SubmissionTransition;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SubmissionStateMachine
{
    private const TRANSITIONS = [
        'submitted'                    => ['under_initial_review', 'rejected'],
        'under_initial_review'         => ['revision_requested', 'under_peer_review', 'rejected'],
        'revision_requested'           => ['under_initial_review'],
        'under_peer_review'            => ['revision_after_review', 'accepted', 'rejected'],
        'revision_after_review'        => ['under_peer_review', 'accepted', 'rejected'],
        'accepted'                     => ['in_production'],
        'in_production'                => ['awaiting_author_approval'],
        'awaiting_author_approval'     => ['published', 'in_production'],
        'published'                    => [],
        'rejected'                     => [],
    ];

    public function __construct(private SubmissionTransitionLogger $logger) {}

    public function transition(
        Submission $submission,
        SubmissionStatus $target,
        User $actor,
        ?string $notes = null,
    ): void {
        $current = $submission->status;

        if (!$current instanceof SubmissionStatus) {
            throw new IllegalTransitionException(
                "Le statut courant n'est pas un enum valide."
            );
        }

        if (!$this->canTransition($current, $target)) {
            throw new IllegalTransitionException(
                "Transition interdite : {$current->value} → {$target->value}"
            );
        }

        $affected = Submission::where('id', $submission->id)
            ->where('status', $current->value)
            ->update(['status' => $target->value]);

        if ($affected === 0) {
            throw new IllegalTransitionException(
                "Le statut a été modifié par un autre utilisateur entre-temps."
            );
        }

        $submission->refresh();

        $this->logger->log(
            submission: $submission,
            action: SubmissionTransition::ACTION_STATUS_CHANGED,
            actor: $actor,
            fromStatus: $current->value,
            toStatus: $target->value,
            notes: $notes,
        );

        if (in_array($target, [SubmissionStatus::Accepted, SubmissionStatus::Rejected], true)) {
            $submission->load('author');
            if ($submission->author) {
                Mail::to($submission->author)->queue(new SubmissionDecision($submission));
            }
        }

        if ($target === SubmissionStatus::AwaitingAuthorApproval) {
            $submission->author_approval_requested_at = now();
            $submission->save();

            $submission->load('author');
            if ($submission->author) {
                Mail::to($submission->author)->queue(new AuthorApprovalRequested($submission));
            }
        }

        if ($target === SubmissionStatus::Published && $current === SubmissionStatus::AwaitingAuthorApproval) {
            $submission->author_approved_at = now();
            $submission->save();

            $recipients = collect();
            $submission->load('editor');
            if ($submission->editor) {
                $recipients->push($submission->editor);
            }
            $chiefEditors = User::whereHas(
                'capabilities',
                fn ($q) => $q->where('capability', EditorialCapability::CHIEF_EDITOR)
            )->get();
            $recipients = $recipients->merge($chiefEditors)->filter()->unique('id');

            foreach ($recipients as $user) {
                Mail::to($user)->queue(new AuthorApproved($submission));
            }
        }
    }

    public function canTransition(SubmissionStatus $from, SubmissionStatus $to): bool
    {
        return in_array($to->value, self::TRANSITIONS[$from->value] ?? [], true);
    }

    /**
     * @return SubmissionStatus[]
     */
    public function allowedNextStatuses(SubmissionStatus $from): array
    {
        return array_map(
            fn(string $v) => SubmissionStatus::from($v),
            self::TRANSITIONS[$from->value] ?? []
        );
    }
}
