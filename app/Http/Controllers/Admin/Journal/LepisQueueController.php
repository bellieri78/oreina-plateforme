<?php

namespace App\Http\Controllers\Admin\Journal;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Submission;

class LepisQueueController extends Controller
{
    public function index()
    {
        $this->authorize('access-lepis-queue');

        $submissions = Submission::with([
                'author',
                'editor',
                'transitions' => fn ($q) => $q
                    ->where('to_status', SubmissionStatus::RejectedPendingLepis->value)
                    ->orderByDesc('created_at')
                    ->with('actor'),
            ])
            ->where('status', SubmissionStatus::RejectedPendingLepis->value)
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.journal.lepis-queue', compact('submissions'));
    }
}
