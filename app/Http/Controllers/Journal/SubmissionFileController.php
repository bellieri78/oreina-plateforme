<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Services\SubmissionFileService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class SubmissionFileController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private SubmissionFileService $files) {}

    public function download(Request $request, Submission $submission, string $path)
    {
        $this->authorize('viewFile', $submission);

        $fullPath = "{$submission->id}/{$path}";

        return $this->files->download($submission, $fullPath);
    }
}
