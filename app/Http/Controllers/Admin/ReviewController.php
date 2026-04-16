<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Mail\ReviewInvitation;
use App\Mail\ReviewReminder;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['submission', 'reviewer']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('submission', fn($sub) => $sub->where('title', 'ilike', "%{$search}%"))
                  ->orWhereHas('reviewer', fn($rev) => $rev->where('name', 'ilike', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('recommendation')) {
            $query->where('recommendation', $request->get('recommendation'));
        }

        if ($request->filled('overdue') && $request->get('overdue') === '1') {
            $query->where('due_date', '<', now())
                  ->whereIn('status', ['invited', 'accepted']);
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $stats = [
            'total' => Review::count(),
            'pending' => Review::whereIn('status', ['invited', 'accepted'])->count(),
            'completed' => Review::where('status', 'completed')->count(),
            'overdue' => Review::where('due_date', '<', now())
                ->whereIn('status', ['invited', 'accepted'])->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function create(Request $request)
    {
        $submissions = Submission::whereIn('status', ['under_initial_review', 'under_peer_review'])
            ->orderBy('created_at', 'desc')
            ->get();
        $reviewers = User::orderBy('name')->get();

        $selectedSubmission = $request->get('submission_id');

        return view('admin.reviews.create', compact('submissions', 'reviewers', 'selectedSubmission'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'reviewer_id' => 'required|exists:users,id',
            'due_date' => 'nullable|date|after:today',
            'status' => 'required|in:invited,accepted,declined,completed,expired',
        ]);

        $validated['assigned_by'] = auth()->id();
        $validated['invited_at'] = now();

        $review = Review::create($validated);

        // Envoyer l'invitation par email au reviewer
        $review->load('reviewer');
        if ($review->reviewer && $review->reviewer->email) {
            Mail::to($review->reviewer)->send(new ReviewInvitation($review));
        }

        return redirect()
            ->route('admin.reviews.show', $review)
            ->with('success', 'Review assignee avec succes. Une invitation a ete envoyee au reviewer.');
    }

    public function show(Review $review)
    {
        $review->load(['submission.author', 'reviewer', 'assignedBy']);

        return view('admin.reviews.show', compact('review'));
    }

    public function edit(Review $review)
    {
        $submissions = Submission::orderBy('created_at', 'desc')->get();
        $reviewers = User::orderBy('name')->get();

        return view('admin.reviews.edit', compact('review', 'submissions', 'reviewers'));
    }

    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'reviewer_id' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
            'status' => 'required|in:invited,accepted,declined,completed,expired',
            'recommendation' => 'nullable|in:accept,minor_revision,major_revision,reject',
            'comments_to_editor' => 'nullable|string',
            'comments_to_author' => 'nullable|string',
            'score_originality' => 'nullable|integer|min:1|max:5',
            'score_methodology' => 'nullable|integer|min:1|max:5',
            'score_clarity' => 'nullable|integer|min:1|max:5',
            'score_significance' => 'nullable|integer|min:1|max:5',
            'score_references' => 'nullable|integer|min:1|max:5',
            'review_file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        // Si on passe en statut completed
        if ($validated['status'] === 'completed' && $review->status !== 'completed') {
            $validated['completed_at'] = now();
        }

        // Si on change le statut en accepted ou declined
        if (in_array($validated['status'], ['accepted', 'declined']) && !$review->responded_at) {
            $validated['responded_at'] = now();
        }

        // Handle review file
        if ($request->has('remove_review_file') && $review->review_file) {
            Storage::disk('public')->delete($review->review_file);
            $validated['review_file'] = null;
        } elseif ($request->hasFile('review_file')) {
            if ($review->review_file) {
                Storage::disk('public')->delete($review->review_file);
            }
            $validated['review_file'] = $request->file('review_file')
                ->store('reviews', 'public');
        }

        $review->update($validated);

        return redirect()
            ->route('admin.reviews.show', $review)
            ->with('success', 'Review mise a jour.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->route('admin.reviews.index')->with('success', 'Review supprimee.');
    }

    public function export(Request $request)
    {
        $query = Review::with(['submission', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('ids')) {
            $ids = explode(',', $request->get('ids'));
            $query->whereIn('id', $ids);
        }

        $reviews = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="reviews_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'Soumission', 'Reviewer', 'Statut', 'Recommandation', 'Echeance', 'Complete le'];

        $callback = function () use ($reviews, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            $statusLabels = [
                'invited' => 'Invite',
                'accepted' => 'Accepte',
                'declined' => 'Decline',
                'completed' => 'Complete',
                'expired' => 'Expire',
            ];

            $recLabels = [
                'accept' => 'Accepter',
                'minor_revision' => 'Revision mineure',
                'major_revision' => 'Revision majeure',
                'reject' => 'Rejeter',
            ];

            foreach ($reviews as $r) {
                fputcsv($file, [
                    $r->id,
                    $r->submission?->title ?? '-',
                    $r->reviewer?->name ?? '-',
                    $statusLabels[$r->status] ?? $r->status,
                    $recLabels[$r->recommendation] ?? '-',
                    $r->due_date?->format('d/m/Y') ?? '-',
                    $r->completed_at?->format('d/m/Y') ?? '-',
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|string']);

        $ids = explode(',', $request->get('ids'));
        $deleted = Review::whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', "{$deleted} review(s) supprimee(s).");
    }

    public function sendReminder(Request $request)
    {
        $request->validate(['ids' => 'required|string']);

        $ids = explode(',', $request->get('ids'));
        $reviews = Review::with(['reviewer', 'submission'])
            ->whereIn('id', $ids)
            ->whereIn('status', ['invited', 'accepted'])
            ->whereHas('reviewer')
            ->get();

        $sent = 0;
        foreach ($reviews as $review) {
            if ($review->reviewer && $review->reviewer->email) {
                Mail::to($review->reviewer)->send(new ReviewReminder($review));
                $sent++;
            }
        }

        return redirect()
            ->route('admin.reviews.index')
            ->with('success', "{$sent} rappel(s) envoye(s).");
    }

    public function download(Review $review)
    {
        if (!$review->review_file || !Storage::disk('public')->exists($review->review_file)) {
            return redirect()->back()->with('error', 'Fichier non trouve.');
        }

        return Storage::disk('public')->download($review->review_file);
    }
}
