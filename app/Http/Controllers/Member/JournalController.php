<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\JournalIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JournalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();
        $isCurrentMember = $member?->isCurrentMember() ?? false;

        // Get all published issues
        $issues = JournalIssue::where('status', 'published')
            ->orderBy('publication_date', 'desc')
            ->paginate(12);

        return view('member.journal', compact(
            'user',
            'member',
            'isCurrentMember',
            'issues'
        ));
    }

    /**
     * Download a journal issue PDF (members only)
     */
    public function download(JournalIssue $issue)
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        // Check if user is a current member
        if (!$member || !$member->isCurrentMember()) {
            return back()->with('error', 'Le téléchargement des numéros est réservé aux adhérents à jour de cotisation.');
        }

        // Check if issue has a PDF
        if (!$issue->pdf_file || !Storage::disk('public')->exists($issue->pdf_file)) {
            return back()->with('error', 'Le PDF de ce numéro n\'est pas disponible.');
        }

        $filename = 'OREINA-' . $issue->volume . '-' . $issue->issue_number . '.pdf';

        return Storage::disk('public')->download($issue->pdf_file, $filename);
    }
}
