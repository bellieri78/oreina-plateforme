<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Mail\AccountInvitation;
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
        return DB::transaction(function () use ($author, $data, $submittedBy) {
            return Submission::create(array_merge($data, [
                'author_id' => $author->id,
                'submitted_by_user_id' => $submittedBy->id === $author->id
                    ? null
                    : $submittedBy->id,
                'status' => SubmissionStatus::Submitted,
                'submitted_at' => now(),
            ]));
        });
    }

    public function createForNewAuthor(
        string $name,
        string $email,
        array $data,
        User $submittedBy,
    ): Submission {
        return DB::transaction(function () use ($name, $email, $data, $submittedBy) {
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

            return $submission;
        });
    }
}
