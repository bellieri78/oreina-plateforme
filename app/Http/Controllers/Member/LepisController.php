<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\LepisSuggestion;
use App\Models\Member;
use Illuminate\Http\Request;

class LepisController extends Controller
{
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

        return redirect()->route('hub.lepis.bulletins.index')->with('success', 'Votre suggestion a bien été envoyée !');
    }
}
