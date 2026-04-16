<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Mail\ReviewerAccepted;
use App\Mail\ReviewerDeclined;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReviewResponseController extends Controller
{
    public function show(Review $review)
    {
        if ($review->status !== Review::STATUS_INVITED) {
            return view('journal.reviews.thanks', [
                'message' => 'Cette invitation a déjà été traitée.',
                'accepted' => null,
                'review' => $review,
            ]);
        }

        $review->load(['submission.author', 'assignedBy']);

        return view('journal.reviews.respond', compact('review'));
    }

    public function accept(Review $review)
    {
        if ($review->status !== Review::STATUS_INVITED) {
            return view('journal.reviews.thanks', [
                'message' => 'Cette invitation a déjà été traitée.',
                'accepted' => null,
                'review' => $review,
            ]);
        }

        $review->update([
            'status' => Review::STATUS_ACCEPTED,
            'responded_at' => now(),
            'due_date' => $review->due_date ?? now()->addDays(21),
        ]);

        $editor = $review->submission?->editor;
        if ($editor) {
            Mail::to($editor)->queue(new ReviewerAccepted($review));
        }

        return view('journal.reviews.thanks', [
            'message' => 'Merci d\'avoir accepté cette invitation de relecture.',
            'accepted' => true,
            'review' => $review,
        ]);
    }

    public function decline(Request $request, Review $review)
    {
        if ($review->status !== Review::STATUS_INVITED) {
            return view('journal.reviews.thanks', [
                'message' => 'Cette invitation a déjà été traitée.',
                'accepted' => null,
                'review' => $review,
            ]);
        }

        $review->update([
            'status' => Review::STATUS_DECLINED,
            'responded_at' => now(),
        ]);

        $editor = $review->submission?->editor;
        if ($editor) {
            Mail::to($editor)->queue(new ReviewerDeclined($review));
        }

        return view('journal.reviews.thanks', [
            'message' => 'Nous avons pris note de votre déclin. Merci de votre réponse.',
            'accepted' => false,
            'review' => $review,
        ]);
    }
}
