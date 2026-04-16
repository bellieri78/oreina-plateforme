<?php

namespace App\Services;

use App\Models\JournalIssue;
use App\Models\Submission;

class PaginationService
{
    public function assignPages(Submission $submission, int $pageCount): void
    {
        $issue = $submission->journalIssue;
        if (!$issue) {
            throw new \InvalidArgumentException('La soumission doit être rattachée à un numéro.');
        }

        $lastPage = Submission::where('journal_issue_id', $issue->id)
            ->where('id', '!=', $submission->id)
            ->whereNotNull('end_page')
            ->max('end_page');

        $startPage = ($lastPage ?? 0) + 1;

        $submission->update([
            'start_page' => $startPage,
            'end_page' => $startPage + $pageCount - 1,
        ]);
    }

    public function getNextStartPage(JournalIssue $issue): int
    {
        $lastPage = Submission::where('journal_issue_id', $issue->id)
            ->whereNotNull('end_page')
            ->max('end_page');

        return ($lastPage ?? 0) + 1;
    }
}
