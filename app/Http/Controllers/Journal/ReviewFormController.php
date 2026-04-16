<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Mail\ReviewCompleted;
use App\Models\Review;
use App\Services\SubmissionTransitionLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ReviewFormController extends Controller
{
    public function show(Review $review)
    {
        abort_unless($this->canAccess($review), 403);
        $review->load('submission');
        return view('journal.reviews.form', compact('review'));
    }

    public function store(Request $request, Review $review, SubmissionTransitionLogger $logger)
    {
        abort_unless($this->canAccess($review), 403);

        $validated = $request->validate([
            'score_originality' => 'required|integer|min:1|max:5',
            'score_methodology' => 'required|integer|min:1|max:5',
            'score_clarity' => 'required|integer|min:1|max:5',
            'score_significance' => 'required|integer|min:1|max:5',
            'score_references' => 'required|integer|min:1|max:5',
            'comments_to_author' => 'required|string|min:50|max:10000',
            'comments_to_editor' => 'nullable|string|max:5000',
            'recommendation' => 'required|in:' . implode(',', array_keys(Review::getRecommendations())),
            'review_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('review_file')) {
            $validated['review_file'] = $request->file('review_file')
                ->store("reviews/{$review->id}", 'submissions');
        }

        $review->update(array_merge($validated, [
            'status' => Review::STATUS_COMPLETED,
            'completed_at' => now(),
        ]));

        $logger->log(
            submission: $review->submission,
            action: 'review_completed',
            actor: Auth::user(),
            target: Auth::user(),
            notes: 'Recommandation : ' . (Review::getRecommendations()[$review->recommendation] ?? $review->recommendation),
        );

        $editor = $review->submission?->editor;
        if ($editor) {
            Mail::to($editor)->queue(new ReviewCompleted($review));
        }

        return redirect()->route('journal.home')
            ->with('success', 'Merci pour votre évaluation. L\'éditeur a été notifié.');
    }

    private function canAccess(Review $review): bool
    {
        return Auth::id() === $review->reviewer_id
            && $review->status === Review::STATUS_ACCEPTED;
    }
}
