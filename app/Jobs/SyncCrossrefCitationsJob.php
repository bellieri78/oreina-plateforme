<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Services\CrossrefCitationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncCrossrefCitationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 15;

    public function __construct(public int $submissionId)
    {
    }

    public function handle(CrossrefCitationService $service): void
    {
        $submission = Submission::find($this->submissionId);
        if (!$submission) {
            return;
        }
        $service->sync($submission);
    }
}
