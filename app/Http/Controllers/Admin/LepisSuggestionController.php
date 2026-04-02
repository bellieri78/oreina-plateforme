<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LepisSuggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LepisSuggestionController extends Controller
{
    public function index(Request $request)
    {
        $query = LepisSuggestion::with('member');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('last_name', 'ilike', "%{$search}%")
                        ->orWhere('first_name', 'ilike', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        $suggestions = $query->orderBy('submitted_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total' => LepisSuggestion::count(),
            'pending' => LepisSuggestion::where('status', 'pending')->count(),
            'noted' => LepisSuggestion::where('status', 'noted')->count(),
        ];

        return view('admin.lepis-suggestions.index', compact('suggestions', 'stats'));
    }

    public function show(LepisSuggestion $suggestion)
    {
        $suggestion->load('member');

        return view('admin.lepis-suggestions.show', compact('suggestion'));
    }

    public function markAsNoted(LepisSuggestion $suggestion)
    {
        $suggestion->update(['status' => 'noted']);

        return back()->with('success', 'Suggestion marquee comme notee.');
    }

    public function destroy(LepisSuggestion $suggestion)
    {
        if ($suggestion->attachment_path) {
            Storage::disk('public')->delete($suggestion->attachment_path);
        }

        $suggestion->delete();

        return redirect()
            ->route('admin.lepis-suggestions.index')
            ->with('success', 'Suggestion supprimee.');
    }
}
