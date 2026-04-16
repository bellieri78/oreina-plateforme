<?php

namespace App\Http\Controllers\Journal;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Mail\NewSubmissionAlert;
use App\Mail\SubmissionReceived;
use App\Models\EditorialCapability;
use App\Models\User;
use App\Rules\SafeUpload;
use App\Services\SubmissionFileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


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
            'cf-turnstile-response' => ['nullable', new \App\Rules\TurnstileCaptcha()],
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
            'status' => SubmissionStatus::Submitted,
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

        // Confirmation email to author
        Mail::to(Auth::user())->queue(new SubmissionReceived($submission));

        // Alert to all editors and chief editors
        $editors = User::where(function ($q) {
            $q->whereHas('capabilities', fn($c) => $c->where('capability', EditorialCapability::EDITOR))
              ->orWhereHas('capabilities', fn($c) => $c->where('capability', EditorialCapability::CHIEF_EDITOR));
        })->get();

        foreach ($editors as $editor) {
            Mail::to($editor)->queue(new NewSubmissionAlert($submission));
        }

        return redirect()->route('journal.submissions.show', $submission)
            ->with('success', 'Votre manuscrit a été soumis avec succès. Vous recevrez une notification lors de son examen.');
    }

    /**
     * Show a specific submission (for the author)
     */
    public function show(Submission $submission)
    {
        if ($submission->author_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette soumission.');
        }

        $submission->load(['transitions' => function ($q) {
            $q->where('action', \App\Models\SubmissionTransition::ACTION_STATUS_CHANGED)
              ->orderBy('created_at', 'desc');
        }]);

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
        if (!in_array($submission->status, [SubmissionStatus::RevisionRequested, SubmissionStatus::RevisionAfterReview], true)) {
            return redirect()->route('journal.submissions.show', $submission)
                ->with('error', 'Cette soumission ne peut pas être modifiée actuellement.');
        }

        return view('journal.submissions.edit', compact('submission'));
    }

    /**
     * Submit a revised manuscript
     */
    public function update(
        Request $request,
        Submission $submission,
        SubmissionFileService $files,
        \App\Services\SubmissionStateMachine $stateMachine,
    ) {
        if ($submission->author_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette soumission.');
        }

        $currentStatus = $submission->status;
        $allowedSources = [
            SubmissionStatus::RevisionRequested,
            SubmissionStatus::RevisionAfterReview,
        ];
        if (!in_array($currentStatus, $allowedSources, true)) {
            return redirect()->route('journal.submissions.show', $submission)
                ->with('error', 'Cette soumission ne peut pas être modifiée actuellement.');
        }

        $validated = $request->validate([
            'manuscript_file' => [
                'required',
                'file',
                new SafeUpload(
                    config('journal.uploads.manuscript.mimes'),
                    config('journal.uploads.manuscript.exts'),
                    config('journal.uploads.manuscript.max_kb'),
                ),
            ],
            'revision_notes' => 'nullable|string|max:5000',
        ]);

        $stored = $files->store(
            $submission,
            $request->file('manuscript_file'),
            SubmissionFileService::TYPE_REVISIONS,
        );

        $submission->manuscript_file = $stored['path'];
        $submission->save();

        $target = $currentStatus === SubmissionStatus::RevisionRequested
            ? SubmissionStatus::UnderInitialReview
            : SubmissionStatus::UnderPeerReview;

        try {
            $stateMachine->transition(
                $submission,
                $target,
                Auth::user(),
                notes: $validated['revision_notes'] ?? null,
            );
        } catch (\App\Exceptions\Editorial\IllegalTransitionException $e) {
            return redirect()->route('journal.submissions.show', $submission)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('journal.submissions.show', $submission)
            ->with('success', 'Votre manuscrit révisé a été soumis avec succès.');
    }
}
