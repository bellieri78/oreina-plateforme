<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Rules\SafeUpload;
use App\Services\SubmissionFileService;
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
    public function store(Request $request, SubmissionFileService $files)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string|min:100|max:3000',
            'keywords' => 'nullable|string|max:500',
            'manuscript_file' => [
                'required',
                'file',
                new SafeUpload(
                    config('journal.uploads.manuscript.mimes'),
                    config('journal.uploads.manuscript.exts'),
                    config('journal.uploads.manuscript.max_kb'),
                ),
            ],
            'supplementary_files' => 'nullable|array|max:10',
            'supplementary_files.*' => [
                'file',
                new SafeUpload(
                    config('journal.uploads.supplementary.mimes'),
                    config('journal.uploads.supplementary.exts'),
                    config('journal.uploads.supplementary.max_kb'),
                ),
            ],
            'co_authors' => 'nullable|array',
            'co_authors.*.name' => 'required_with:co_authors|string|max:255',
            'co_authors.*.email' => 'nullable|email|max:255',
            'co_authors.*.affiliation' => 'nullable|string|max:255',
            'accept_terms' => 'required|accepted',
        ], [
            'manuscript_file.required' => 'Le manuscrit est obligatoire.',
            'accept_terms.accepted' => 'Vous devez accepter les conditions de soumission.',
        ]);

        // Création de la soumission d'abord pour avoir un id
        $submission = Submission::create([
            'author_id' => Auth::id(),
            'title' => $validated['title'],
            'abstract' => $validated['abstract'],
            'keywords' => !empty($validated['keywords']) ? array_values(array_filter(array_map('trim', explode(',', $validated['keywords'])))) : [],
            'co_authors' => $validated['co_authors'] ?? [],
            'manuscript_file' => 'pending',
            'status' => Submission::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        // Stockage du manuscrit
        $stored = $files->store($submission, $request->file('manuscript_file'), SubmissionFileService::TYPE_MANUSCRIPT);
        $submission->manuscript_file = $stored['path'];

        // Stockage des fichiers supplémentaires
        $supp = [];
        foreach ($request->file('supplementary_files', []) as $suppFile) {
            $supp[] = $files->store($submission, $suppFile, SubmissionFileService::TYPE_SUPPLEMENTARY);
        }
        $submission->supplementary_files = $supp;
        $submission->save();

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
