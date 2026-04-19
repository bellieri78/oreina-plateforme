<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Exceptions\Editorial\AlreadyAssignedException;
use App\Exceptions\Editorial\IneligibleUserException;
use App\Exceptions\Editorial\RoleConflictException;
use App\Mail\ReviewInvitation;
use App\Models\EditorialCapability;
use App\Models\Review;
use App\Models\Submission;
use App\Models\SubmissionTransition;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EditorialAssignmentService
{
    public const OVERRIDE_NOTE = 'Override: séparation des rôles forcée';

    public function __construct(
        private SubmissionTransitionLogger $logger,
        private SubmissionStateMachine $stateMachine,
    ) {}

    /**
     * Compose la note de transition quand l'override est forcé.
     * Préserve la forme OVERRIDE_NOTE historique pour compatibilité (docs, stats),
     * et y ajoute le motif utilisateur s'il est fourni.
     */
    private function overrideNote(?string $overrideReason): string
    {
        if ($overrideReason === null || trim($overrideReason) === '') {
            return self::OVERRIDE_NOTE;
        }
        return self::OVERRIDE_NOTE . ' — Motif : ' . trim($overrideReason);
    }

    public function assignEditor(
        Submission $submission,
        User $target,
        User $actor,
        bool $override = false,
        ?string $overrideReason = null,
    ): void {
        $this->assertCapability($target, EditorialCapability::EDITOR);

        if (!$override && $submission->reviews()->where('reviewer_id', $target->id)->exists()) {
            throw new RoleConflictException('Cet utilisateur est déjà relecteur sur cet article.');
        }

        $submission->update(['editor_id' => $target->id]);

        $this->logger->log(
            submission: $submission,
            action: SubmissionTransition::ACTION_EDITOR_ASSIGNED,
            actor: $actor,
            target: $target,
            notes: $override ? $this->overrideNote($overrideReason) : null,
        );

        $submission->refresh();

        if ($submission->status === SubmissionStatus::Submitted) {
            $this->stateMachine->transition(
                $submission,
                SubmissionStatus::UnderInitialReview,
                $actor,
                notes: 'Transition automatique suite à prise en charge éditeur'
            );
        }
    }

    public function takeEditor(Submission $submission, User $actor): void
    {
        $this->assertCapability($actor, EditorialCapability::EDITOR);

        if ($submission->reviews()->where('reviewer_id', $actor->id)->exists()) {
            throw new RoleConflictException('Vous êtes déjà relecteur sur cet article.');
        }

        // Update conditionnel pour gérer la race condition
        $affected = Submission::where('id', $submission->id)
            ->whereNull('editor_id')
            ->update(['editor_id' => $actor->id]);

        if ($affected === 0) {
            throw new AlreadyAssignedException('Cet article a déjà un éditeur assigné.');
        }

        $submission->refresh();

        $this->logger->log(
            submission: $submission,
            action: SubmissionTransition::ACTION_EDITOR_TAKEN,
            actor: $actor,
            target: $actor,
        );

        if ($submission->status === SubmissionStatus::Submitted) {
            $this->stateMachine->transition(
                $submission,
                SubmissionStatus::UnderInitialReview,
                $actor,
                notes: 'Transition automatique suite à prise en charge éditeur'
            );
        }
    }

    public function revokeEditor(Submission $submission, User $actor): void
    {
        if ($submission->editor_id === null) {
            return;
        }
        $formerEditor = $submission->editor;
        $submission->update(['editor_id' => null]);

        $this->logger->log(
            submission: $submission,
            action: SubmissionTransition::ACTION_EDITOR_REVOKED,
            actor: $actor,
            target: $formerEditor,
        );
    }

    public function assignLayoutEditor(Submission $submission, User $target, User $actor): void
    {
        $this->assertCapability($target, EditorialCapability::LAYOUT_EDITOR);

        $submission->update(['layout_editor_id' => $target->id]);

        $this->logger->log(
            submission: $submission,
            action: SubmissionTransition::ACTION_LAYOUT_EDITOR_ASSIGNED,
            actor: $actor,
            target: $target,
        );
    }

    public function assignReviewer(
        Submission $submission,
        User $target,
        User $actor,
        bool $override = false,
        ?string $overrideReason = null,
    ): void {
        $this->assertCapability($target, EditorialCapability::REVIEWER);

        if (!$override && $submission->editor_id === $target->id) {
            throw new RoleConflictException('Cet utilisateur est l\'éditeur de l\'article.');
        }

        if ($submission->reviews()->where('reviewer_id', $target->id)->exists()) {
            throw new AlreadyAssignedException('Cet utilisateur est déjà relecteur sur cet article.');
        }

        $review = Review::create([
            'submission_id' => $submission->id,
            'reviewer_id' => $target->id,
            'assigned_by' => $actor->id,
            'status' => Review::STATUS_INVITED,
            'invited_at' => now(),
        ]);

        $this->logger->log(
            submission: $submission,
            action: SubmissionTransition::ACTION_REVIEWER_INVITED,
            actor: $actor,
            target: $target,
            notes: $override ? $this->overrideNote($overrideReason) : null,
        );

        Mail::to($target)->queue(new ReviewInvitation($review));
    }

    private function assertCapability(User $user, string $capability): void
    {
        if (!$user->hasCapability($capability)) {
            throw new IneligibleUserException(
                "L'utilisateur n'a pas la capacité requise : {$capability}"
            );
        }
    }
}
