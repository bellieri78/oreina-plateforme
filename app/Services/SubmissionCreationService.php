<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Mail\AccountInvitation;
use App\Mail\NewSubmissionAlert;
use App\Mail\SubmissionReceived;
use App\Models\EditorialCapability;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\DB;

class SubmissionCreationService
{
    public function __construct(
        private Mailer $mailer,
    ) {}

    public function createForExistingAuthor(
        User $author,
        array $data,
        User $submittedBy,
    ): Submission {
        $submission = DB::transaction(function () use ($author, $data, $submittedBy) {
            return Submission::create(array_merge($data, [
                'author_id' => $author->id,
                'submitted_by_user_id' => $submittedBy->id === $author->id
                    ? null
                    : $submittedBy->id,
                'status' => SubmissionStatus::Submitted,
                'submitted_at' => now(),
            ]));
        });

        $this->mailer->to($author)->queue(new SubmissionReceived($submission));
        $this->notifyEditors($submission);

        return $submission;
    }

    public function createForNewAuthor(
        string $name,
        string $email,
        array $data,
        User $submittedBy,
    ): Submission {
        [$submission, $author] = DB::transaction(function () use ($name, $email, $data, $submittedBy) {
            $author = User::create([
                'name' => $name,
                'email' => $email,
                'password' => null,
                'invited_at' => now(),
                'invited_by_user_id' => $submittedBy->id,
            ]);

            $submission = Submission::create(array_merge($data, [
                'author_id' => $author->id,
                'submitted_by_user_id' => $submittedBy->id,
                'status' => SubmissionStatus::Submitted,
                'submitted_at' => now(),
            ]));

            $this->mailer
                ->to($author->email)
                ->queue(new AccountInvitation($author, $submission, $submittedBy));

            return [$submission, $author];
        });

        $this->notifyEditors($submission);

        return $submission;
    }

    private function notifyEditors(Submission $submission): void
    {
        $editors = User::query()
            ->whereHas('capabilities', fn ($q) => $q->whereIn('capability', [
                EditorialCapability::EDITOR,
                EditorialCapability::CHIEF_EDITOR,
            ]))
            ->get();

        foreach ($editors as $editor) {
            $this->mailer->to($editor)->queue(new NewSubmissionAlert($submission));
        }
    }
}
