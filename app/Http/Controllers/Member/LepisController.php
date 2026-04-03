<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\LepisBulletin;
use App\Models\LepisSuggestion;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LepisController extends Controller
{
    public function index()
    {
        $bulletins = LepisBulletin::published()
            ->orderBy('year', 'desc')
            ->orderBy('issue_number', 'desc')
            ->paginate(12);

        return view('member.lepis.index', compact('bulletins'));
    }

    public function download(LepisBulletin $bulletin)
    {
        if (!$bulletin->is_published) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($bulletin->pdf_path)) {
            abort(404, 'PDF non trouvé');
        }

        return Storage::disk('public')->download(
            $bulletin->pdf_path,
            "Lepis_{$bulletin->issue_number}_{$bulletin->quarter}_{$bulletin->year}.pdf"
        );
    }

    public function suggest()
    {
        return view('member.lepis.suggest');
    }

    public function storeSuggestion(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'attachment' => 'nullable|file|max:10240',
        ]);

        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->firstOrFail();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('lepis-suggestions', 'public');
        }

        LepisSuggestion::create([
            'member_id' => $member->id,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'attachment_path' => $attachmentPath,
            'submitted_at' => now(),
        ]);

        return redirect()->route('member.lepis')->with('success', 'Votre suggestion a bien été envoyée !');
    }
}
