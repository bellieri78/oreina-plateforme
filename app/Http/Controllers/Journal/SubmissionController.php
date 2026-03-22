<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    /**
     * Show the submission form
     */
    public function create()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('message', 'Vous devez être connecté pour soumettre un article.');
        }

        return view('journal.submissions.create');
    }

    /**
     * Store a new submission
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string|min:100|max:3000',
            'keywords' => 'required|string|max:255',
            'manuscript_file' => 'required|file|mimes:pdf|max:20480', // 20MB max
            'co_authors' => 'nullable|array',
            'co_authors.*.name' => 'required|string|max:255',
            'co_authors.*.email' => 'nullable|email|max:255',
            'co_authors.*.affiliation' => 'nullable|string|max:255',
            'accept_terms' => 'required|accepted',
        ], [
            'title.required' => 'Le titre est obligatoire.',
            'abstract.required' => 'Le résumé est obligatoire.',
            'abstract.min' => 'Le résumé doit contenir au moins 100 caractères.',
            'abstract.max' => 'Le résumé ne peut pas dépasser 3000 caractères.',
            'keywords.required' => 'Les mots-clés sont obligatoires.',
            'manuscript_file.required' => 'Le manuscrit PDF est obligatoire.',
            'manuscript_file.mimes' => 'Le manuscrit doit être au format PDF.',
            'manuscript_file.max' => 'Le manuscrit ne peut pas dépasser 20 Mo.',
            'accept_terms.accepted' => 'Vous devez accepter les conditions de soumission.',
        ]);

        // Store the manuscript file
        $manuscriptPath = $request->file('manuscript_file')
            ->store('submissions', 'public');

        // Create the submission
        $submission = Submission::create([
            'author_id' => Auth::id(),
            'title' => $validated['title'],
            'abstract' => $validated['abstract'],
            'keywords' => $validated['keywords'],
            'manuscript_file' => $manuscriptPath,
            'co_authors' => $validated['co_authors'] ?? [],
            'status' => Submission::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        return redirect()->route('journal.submissions.show', $submission)
            ->with('success', 'Votre manuscrit a été soumis avec succès. Vous recevrez une notification lors de son examen.');
    }

    /**
     * Show a specific submission (for the author)
     */
    public function show(Submission $submission)
    {
        // Check authorization
        if ($submission->author_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette soumission.');
        }

        return view('journal.submissions.show', compact('submission'));
    }

    /**
     * Show all submissions for the current user
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('message', 'Vous devez être connecté pour voir vos soumissions.');
        }

        $submissions = Submission::where('author_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('journal.submissions.index', compact('submissions'));
    }

    /**
     * Show the revision form
     */
    public function edit(Submission $submission)
    {
        // Check authorization
        if ($submission->author_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette soumission.');
        }

        // Check if revision is requested
        if ($submission->status !== Submission::STATUS_REVISION) {
            return redirect()->route('journal.submissions.show', $submission)
                ->with('error', 'Cette soumission ne peut pas être modifiée actuellement.');
        }

        return view('journal.submissions.edit', compact('submission'));
    }

    /**
     * Submit a revised manuscript
     */
    public function update(Request $request, Submission $submission)
    {
        // Check authorization
        if ($submission->author_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette soumission.');
        }

        // Check if revision is requested
        if ($submission->status !== Submission::STATUS_REVISION) {
            return redirect()->route('journal.submissions.show', $submission)
                ->with('error', 'Cette soumission ne peut pas être modifiée actuellement.');
        }

        $validated = $request->validate([
            'manuscript_file' => 'required|file|mimes:pdf|max:20480',
            'revision_notes' => 'nullable|string|max:5000',
        ]);

        // Delete old manuscript
        if ($submission->manuscript_file) {
            Storage::disk('public')->delete($submission->manuscript_file);
        }

        // Store new manuscript
        $manuscriptPath = $request->file('manuscript_file')
            ->store('submissions', 'public');

        // Update submission
        $submission->update([
            'manuscript_file' => $manuscriptPath,
            'status' => Submission::STATUS_IN_REVIEW, // Back to review
        ]);

        return redirect()->route('journal.submissions.show', $submission)
            ->with('success', 'Votre manuscrit révisé a été soumis avec succès.');
    }
}
