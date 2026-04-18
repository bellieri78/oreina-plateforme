<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\JournalIssue;
use App\Models\Submission;
use App\Models\User;
use App\Services\ArticleLatexService;
use App\Services\ArticlePdfService;
use App\Services\CrossrefService;
use App\Services\DocumentConversionService;
use App\Services\MarkdownToBlocksService;
use App\Services\PaginationService;
use App\Services\SubmissionTransitionLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Submission::with(['author', 'journalIssue'])->withCount('reviews');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhereHas('author', fn($q) => $q->where('name', 'ilike', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('journal_issue_id')) {
            $query->where('journal_issue_id', $request->get('journal_issue_id'));
        }

        if ($request->filled('decision')) {
            $query->where('decision', $request->get('decision'));
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['created_at', 'submitted_at', 'title'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $submissions = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Submission::count(),
            'pending' => Submission::whereIn('status', ['submitted', 'under_initial_review', 'under_peer_review'])->count(),
            'revision' => Submission::whereIn('status', ['revision_requested', 'revision_after_review'])->count(),
            'accepted' => Submission::where('status', 'accepted')->count(),
            'published' => Submission::where('status', 'published')->count(),
        ];

        $issues = JournalIssue::orderBy('volume_number', 'desc')
            ->orderBy('issue_number', 'desc')
            ->get();

        return view('admin.submissions.index', compact('submissions', 'stats', 'issues'));
    }

    public function create(Request $request)
    {
        $this->authorize('create-submission-for-author');

        $authors = User::orderBy('name')->get();
        $issues = JournalIssue::orderBy('volume_number', 'desc')
            ->orderBy('issue_number', 'desc')
            ->get();

        $selectedIssue = $request->get('journal_issue_id');

        return view('admin.submissions.create', compact('authors', 'issues', 'selectedIssue'));
    }

    public function store(Request $request, \App\Services\SubmissionCreationService $creation)
    {
        $this->authorize('create-submission-for-author');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string|min:100',
            'keywords' => 'nullable|string|max:500',
            'author_mode' => 'required|in:existing,new',
            'author_id' => 'required_if:author_mode,existing|nullable|exists:users,id',
            'author_name' => 'required_if:author_mode,new|nullable|string|max:255',
            'author_email' => [
                'required_if:author_mode,new',
                'nullable',
                'email',
                \Illuminate\Validation\Rule::unique('users', 'email'),
            ],
            'journal_issue_id' => 'nullable|exists:journal_issues,id',
            'editor_id' => 'nullable|exists:users,id',
            'editor_notes' => 'nullable|string',
            'doi' => 'nullable|string|max:255',
            'start_page' => 'nullable|integer|min:1',
            'end_page' => 'nullable|integer|min:1',
            'manuscript_file' => 'required|file|mimes:doc,docx,pdf,odt|max:30720',
            'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
            'featured_image' => 'nullable|image|max:5120',
        ], [
            'author_email.unique' => 'Un compte existe déjà pour cet email. Sélectionnez « Auteur existant » dans la liste déroulante.',
        ]);

        // Extract non-file fields we want to pass to the service
        $data = collect($validated)->only([
            'title', 'abstract', 'keywords', 'journal_issue_id',
            'editor_id', 'editor_notes', 'doi', 'start_page', 'end_page',
        ])->toArray();

        // Handle file uploads
        $data['manuscript_file'] = $request->file('manuscript_file')
            ->store('submissions/manuscripts', 'public');

        if ($request->hasFile('pdf_file')) {
            $data['pdf_file'] = $request->file('pdf_file')
                ->store('submissions/pdfs', 'public');
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')
                ->store('submissions/images', 'public');
        }

        $submittedBy = $request->user();

        if ($validated['author_mode'] === 'existing') {
            $author = User::findOrFail($validated['author_id']);
            $submission = $creation->createForExistingAuthor($author, $data, $submittedBy);
        } else {
            $submission = $creation->createForNewAuthor(
                $validated['author_name'],
                $validated['author_email'],
                $data,
                $submittedBy,
            );
        }

        return redirect()
            ->route('admin.submissions.show', $submission)
            ->with('success', 'Soumission creee avec succes.');
    }

    public function show(Submission $submission)
    {
        $submission->load([
            'author',
            'editor',
            'layoutEditor',
            'journalIssue',
            'reviews.reviewer',
            'transitions' => fn($q) => $q->with(['actor', 'target'])->orderBy('created_at', 'desc'),
        ]);

        $eligibleReviewers = User::withCapability(\App\Models\EditorialCapability::REVIEWER)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $eligibleEditors = User::withCapability(\App\Models\EditorialCapability::EDITOR)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $eligibleLayoutEditors = User::withCapability(\App\Models\EditorialCapability::LAYOUT_EDITOR)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.submissions.show', compact(
            'submission',
            'eligibleReviewers',
            'eligibleEditors',
            'eligibleLayoutEditors',
        ));
    }

    public function edit(Submission $submission)
    {
        $authors = User::orderBy('name')->get();
        $editors = User::orderBy('name')->get();
        $issues = JournalIssue::orderBy('volume_number', 'desc')
            ->orderBy('issue_number', 'desc')
            ->get();

        return view('admin.submissions.edit', compact('submission', 'authors', 'editors', 'issues'));
    }

    public function update(Request $request, Submission $submission)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'nullable|string',
            'keywords' => 'nullable|string|max:500',
            'author_id' => 'required|exists:users,id',
            'journal_issue_id' => 'nullable|exists:journal_issues,id',
            'status' => 'required|in:submitted,under_initial_review,revision_requested,under_peer_review,revision_after_review,in_production,awaiting_author_approval,accepted,rejected,published',
            'editor_id' => 'nullable|exists:users,id',
            'editor_notes' => 'nullable|string',
            'decision' => 'nullable|in:accept,minor_revision,major_revision,reject',
            'doi' => 'nullable|string|max:255',
            'start_page' => 'nullable|integer|min:1',
            'end_page' => 'nullable|integer|min:1',
            'manuscript_file' => 'nullable|file|mimes:doc,docx,pdf,odt|max:20480',
            'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
            'featured_image' => 'nullable|image|max:5120',
            // Content fields
            'content_html' => 'nullable|string',
            'content_blocks' => 'nullable|string',
            'references' => 'nullable|string',
            'acknowledgements' => 'nullable|string',
            'author_affiliations' => 'nullable|string',
            'received_at' => 'nullable|date',
            'accepted_at' => 'nullable|date',
        ]);

        // Process content_blocks JSON
        if (!empty($validated['content_blocks'])) {
            $blocks = json_decode($validated['content_blocks'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['content_blocks'] = $blocks;
                // Generate HTML from blocks for display
                $validated['content_html'] = $this->blocksToHtml($blocks);
            }
        }

        // Si decision prise
        if (!empty($validated['decision']) && $submission->decision !== $validated['decision']) {
            $validated['decision_at'] = now();
        }

        // Si publication
        if ($validated['status'] === 'published' && $submission->status !== 'published') {
            $validated['published_at'] = now();
        }

        // Process references as array (one per line)
        if (!empty($validated['references'])) {
            $validated['references'] = array_filter(
                array_map('trim', explode("\n", $validated['references']))
            );
        }

        // Process author affiliations as array (one per line)
        if (!empty($validated['author_affiliations'])) {
            $validated['author_affiliations'] = array_filter(
                array_map('trim', explode("\n", $validated['author_affiliations']))
            );
        }

        // Handle manuscript file
        if ($request->has('remove_manuscript') && $submission->manuscript_file) {
            Storage::disk('public')->delete($submission->manuscript_file);
            $validated['manuscript_file'] = null;
        } elseif ($request->hasFile('manuscript_file')) {
            if ($submission->manuscript_file) {
                Storage::disk('public')->delete($submission->manuscript_file);
            }
            $validated['manuscript_file'] = $request->file('manuscript_file')
                ->store('submissions/manuscripts', 'public');
        }

        // Handle PDF file
        if ($request->has('remove_pdf') && $submission->pdf_file) {
            Storage::disk('public')->delete($submission->pdf_file);
            $validated['pdf_file'] = null;
        } elseif ($request->hasFile('pdf_file')) {
            if ($submission->pdf_file) {
                Storage::disk('public')->delete($submission->pdf_file);
            }
            $validated['pdf_file'] = $request->file('pdf_file')
                ->store('submissions/pdfs', 'public');
        }

        // Handle featured image
        if ($request->has('remove_featured_image') && $submission->featured_image) {
            Storage::disk('public')->delete($submission->featured_image);
            $validated['featured_image'] = null;
        } elseif ($request->hasFile('featured_image')) {
            if ($submission->featured_image) {
                Storage::disk('public')->delete($submission->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')
                ->store('submissions/images', 'public');
        }

        $submission->update($validated);

        return redirect()
            ->route('admin.submissions.show', $submission)
            ->with('success', 'Soumission mise a jour.');
    }

    public function destroy(Submission $submission)
    {
        if ($submission->reviews()->count() > 0) {
            return redirect()
                ->route('admin.submissions.index')
                ->with('error', 'Impossible de supprimer une soumission avec des reviews.');
        }

        $submission->delete();
        return redirect()->route('admin.submissions.index')->with('success', 'Soumission supprimee.');
    }

    public function export(Request $request)
    {
        $query = Submission::with(['author', 'journalIssue']);

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('journal_issue_id')) {
            $query->where('journal_issue_id', $request->get('journal_issue_id'));
        }

        if ($request->filled('ids')) {
            $ids = explode(',', $request->get('ids'));
            $query->whereIn('id', $ids);
        }

        $submissions = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="soumissions_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'Titre', 'Auteur', 'Numero', 'Statut', 'Decision', 'Soumis le', 'DOI', 'Pages'];

        $callback = function () use ($submissions, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($submissions as $s) {
                fputcsv($file, [
                    $s->id,
                    $s->title,
                    $s->author?->name ?? '-',
                    $s->journalIssue ? "Vol.{$s->journalIssue->volume_number} N°{$s->journalIssue->issue_number}" : '-',
                    Submission::getStatuses()[$s->status] ?? $s->status,
                    $s->decision ? Submission::getDecisions()[$s->decision] : '-',
                    $s->submitted_at?->format('d/m/Y') ?? '-',
                    $s->doi ?? '-',
                    $s->page_range ?? '-',
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

        // Check for submissions with reviews
        $hasReviews = Submission::whereIn('id', $ids)->whereHas('reviews')->count();
        if ($hasReviews > 0) {
            return redirect()
                ->route('admin.submissions.index')
                ->with('error', 'Certaines soumissions ont des reviews et ne peuvent pas etre supprimees.');
        }

        $deleted = Submission::whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.submissions.index')
            ->with('success', "{$deleted} soumission(s) supprimee(s).");
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'status' => 'required|in:submitted,under_initial_review,revision_requested,under_peer_review,revision_after_review,in_production,awaiting_author_approval,accepted,rejected,published',
        ]);

        $ids = explode(',', $request->get('ids'));
        $data = ['status' => $request->get('status')];

        if ($request->get('status') === 'published') {
            $data['published_at'] = now();
        }

        $updated = Submission::whereIn('id', $ids)->update($data);

        return redirect()
            ->route('admin.submissions.index')
            ->with('success', "{$updated} soumission(s) mise(s) a jour.");
    }

    public function bulkAssignIssue(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'journal_issue_id' => 'required|exists:journal_issues,id',
        ]);

        $ids = explode(',', $request->get('ids'));
        $updated = Submission::whereIn('id', $ids)->update([
            'journal_issue_id' => $request->get('journal_issue_id')
        ]);

        return redirect()
            ->route('admin.submissions.index')
            ->with('success', "{$updated} soumission(s) assignee(s) au numero.");
    }

    public function download(Submission $submission, string $type)
    {
        $file = match($type) {
            'manuscript' => $submission->manuscript_file,
            'pdf' => $submission->pdf_file,
            default => null,
        };

        if (!$file || !Storage::disk('public')->exists($file)) {
            return redirect()->back()->with('error', 'Fichier non trouve.');
        }

        return Storage::disk('public')->download($file);
    }

    /**
     * Generate PDF for an article (using LaTeX)
     */
    public function generatePdf(Submission $submission, ArticleLatexService $latexService)
    {
        if (!$latexService->canGeneratePdf($submission)) {
            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('error', 'Le PDF ne peut être généré que pour les articles acceptés ou publiés.');
        }

        try {
            $latexService->generatePdf($submission);

            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('success', 'PDF généré avec succès (LaTeX).');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }

    /**
     * Preview PDF (stream in browser)
     */
    public function previewPdf(Submission $submission, ArticleLatexService $latexService)
    {
        if (!$latexService->canGeneratePdf($submission)) {
            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('error', 'Le PDF ne peut être prévisualisé que pour les articles acceptés ou publiés.');
        }

        try {
            return $latexService->stream($submission);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.submissions.layout', $submission)
                ->with('error', 'Erreur lors de la génération du PDF : ' . $e->getMessage());
        }
    }

    /**
     * Download generated PDF
     */
    public function downloadPdf(Submission $submission, ArticleLatexService $latexService)
    {
        if (!$latexService->canGeneratePdf($submission)) {
            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('error', 'Le PDF ne peut être téléchargé que pour les articles acceptés ou publiés.');
        }

        return $latexService->download($submission);
    }

    /**
     * Register DOI with Crossref
     */
    public function registerDoi(Submission $submission, CrossrefService $crossrefService)
    {
        if (!in_array($submission->status, [SubmissionStatus::Accepted, SubmissionStatus::InProduction, SubmissionStatus::Published])) {
            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('error', 'Le DOI ne peut être enregistré que pour les articles acceptés ou publiés.');
        }

        $result = $crossrefService->registerDoi($submission);

        if ($result['success']) {
            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('success', $result['message']);
        } else {
            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('error', $result['error']);
        }
    }

    /**
     * Assign DOI locally (without Crossref registration)
     */
    public function assignDoi(Submission $submission, CrossrefService $crossrefService)
    {
        if (!in_array($submission->status, [SubmissionStatus::Accepted, SubmissionStatus::InProduction, SubmissionStatus::Published])) {
            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('error', 'Le DOI ne peut être assigné que pour les articles acceptés ou publiés.');
        }

        $doi = $crossrefService->assignLocalDoi($submission);

        return redirect()
            ->route('admin.submissions.show', $submission)
            ->with('success', "DOI assigné: {$doi}");
    }

    /**
     * Publish an article (change status + generate PDF + assign DOI)
     */
    public function publish(Request $request, Submission $submission, ArticleLatexService $latexService, CrossrefService $crossrefService)
    {
        if ($submission->status !== SubmissionStatus::Accepted) {
            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('error', 'Seuls les articles acceptés peuvent être publiés.');
        }

        $validated = $request->validate([
            'journal_issue_id' => 'required|exists:journal_issues,id',
            'start_page' => 'required|integer|min:1',
            'end_page' => 'required|integer|min:1|gte:start_page',
            'generate_pdf' => 'boolean',
            'register_doi' => 'boolean',
        ]);

        // Update submission
        $submission->update([
            'journal_issue_id' => $validated['journal_issue_id'],
            'start_page' => $validated['start_page'],
            'end_page' => $validated['end_page'],
            'status' => SubmissionStatus::Published,
            'published_at' => now(),
        ]);

        $messages = ['Article publié avec succès.'];

        // Generate PDF if requested (using LaTeX)
        if ($request->boolean('generate_pdf', true)) {
            try {
                $latexService->generatePdf($submission);
                $messages[] = 'PDF généré (LaTeX).';
            } catch (\Exception $e) {
                $messages[] = 'Erreur PDF: ' . $e->getMessage();
            }
        }

        // Register DOI if requested
        if ($request->boolean('register_doi', false)) {
            $result = $crossrefService->registerDoi($submission);
            if ($result['success']) {
                $messages[] = "DOI enregistré: {$result['doi']}";
            } else {
                $messages[] = "Erreur DOI: {$result['error']}";
            }
        } elseif (!$submission->doi) {
            // Assign local DOI if not registering with Crossref
            $doi = $crossrefService->assignLocalDoi($submission);
            $messages[] = "DOI assigné: {$doi}";
        }

        return redirect()
            ->route('admin.submissions.show', $submission)
            ->with('success', implode(' ', $messages));
    }

    /**
     * Convert content blocks to HTML
     */
    private function blocksToHtml(array $blocks): string
    {
        $html = '';

        foreach ($blocks as $block) {
            $type = $block['type'] ?? 'paragraph';

            switch ($type) {
                case 'heading':
                    $level = $block['level'] ?? 'h2';
                    $text = e($block['content'] ?? '');
                    $html .= "<{$level}>{$text}</{$level}>\n";
                    break;

                case 'paragraph':
                    $content = $block['content'] ?? '';
                    $html .= "<p>{$content}</p>\n";
                    break;

                case 'image':
                    $src = e($block['url'] ?? $block['src'] ?? '');
                    $caption = e($block['caption'] ?? '');
                    if ($src) {
                        $html .= "<figure>\n";
                        $html .= "  <img src=\"{$src}\" alt=\"{$caption}\">\n";
                        if ($caption) {
                            $html .= "  <figcaption>{$caption}</figcaption>\n";
                        }
                        $html .= "</figure>\n";
                    }
                    break;

                case 'table':
                    $data = $block['data'] ?? [];
                    if (!empty($data)) {
                        $html .= "<table>\n<tbody>\n";
                        foreach ($data as $row) {
                            $html .= "<tr>\n";
                            foreach ($row as $cell) {
                                $html .= "  <td>" . e($cell) . "</td>\n";
                            }
                            $html .= "</tr>\n";
                        }
                        $html .= "</tbody>\n</table>\n";
                    }
                    break;

                case 'list':
                    $items = $block['items'] ?? [];
                    $ordered = $block['ordered'] ?? false;
                    $tag = $ordered ? 'ol' : 'ul';
                    if (!empty($items)) {
                        $html .= "<{$tag}>\n";
                        foreach ($items as $item) {
                            $html .= "  <li>" . e($item) . "</li>\n";
                        }
                        $html .= "</{$tag}>\n";
                    }
                    break;

                case 'quote':
                    $content = e($block['content'] ?? '');
                    $source = e($block['source'] ?? '');
                    $html .= "<blockquote>\n";
                    $html .= "  <p>{$content}</p>\n";
                    if ($source) {
                        $html .= "  <cite>— {$source}</cite>\n";
                    }
                    $html .= "</blockquote>\n";
                    break;
            }
        }

        return $html;
    }

    public function assignPages(Request $request, Submission $submission, PaginationService $pagination)
    {
        $validated = $request->validate([
            'page_count' => 'required|integer|min:1|max:500',
        ]);

        if (!$submission->journal_issue_id) {
            return back()->with('error', 'La soumission doit être rattachée à un numéro avant d\'assigner la pagination.');
        }

        $pagination->assignPages($submission, (int) $validated['page_count']);

        $submission->refresh();

        return back()->with('success', "Pagination assignée : pp. {$submission->start_page}–{$submission->end_page}.");
    }

    /**
     * Show the layout editor for an article
     */
    public function layout(Submission $submission)
    {
        // Only allow layout for accepted or published articles
        if (!in_array($submission->status, [SubmissionStatus::Accepted, SubmissionStatus::InProduction, SubmissionStatus::Published])) {
            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('error', 'La maquette ne peut être éditée que pour les articles acceptés ou publiés.');
        }

        // Auto-transition accepted → in_production when opening layout editor
        if ($submission->status === SubmissionStatus::Accepted) {
            $submission->update(['status' => SubmissionStatus::InProduction]);
            app(SubmissionTransitionLogger::class)->log(
                $submission,
                'status_changed',
                auth()->user(),
                fromStatus: SubmissionStatus::Accepted->value,
                toStatus: SubmissionStatus::InProduction->value,
                notes: 'Passage automatique en maquettage (ouverture éditeur)',
            );
        }

        $submission->load(['author', 'journalIssue']);

        return view('admin.submissions.layout', compact('submission'));
    }

    /**
     * Update the layout/content of an article
     */
    public function updateLayout(Request $request, Submission $submission)
    {
        // Only allow layout for accepted or published articles
        if (!in_array($submission->status, [SubmissionStatus::Accepted, SubmissionStatus::InProduction, SubmissionStatus::Published])) {
            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('error', 'La maquette ne peut être éditée que pour les articles acceptés ou publiés.');
        }

        $validated = $request->validate([
            'content_blocks' => 'nullable|string',
            'references' => 'nullable|string',
            'acknowledgements' => 'nullable|string',
            'author_affiliations' => 'nullable|string',
            'display_authors' => 'nullable|string|max:1000',
            'title_en' => 'nullable|string|max:500',
            'display_abstract' => 'nullable|string',
            'display_summary' => 'nullable|string',
            'received_at' => 'nullable|date',
            'accepted_at' => 'nullable|date',
        ]);

        // Process content_blocks JSON
        if (!empty($validated['content_blocks'])) {
            $blocks = json_decode($validated['content_blocks'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['content_blocks'] = $blocks;
                // Generate HTML from blocks for display
                $validated['content_html'] = $this->blocksToHtml($blocks);
            }
        }

        // Process references as array (one per line)
        if (!empty($validated['references'])) {
            $validated['references'] = array_filter(
                array_map('trim', explode("\n", $validated['references']))
            );
        }

        // Process author affiliations as array (one per line)
        if (!empty($validated['author_affiliations'])) {
            $validated['author_affiliations'] = array_filter(
                array_map('trim', explode("\n", $validated['author_affiliations']))
            );
        }

        $submission->update($validated);

        return redirect()
            ->route('admin.submissions.layout', $submission)
            ->with('success', 'Maquette mise à jour avec succès.');
    }

    /**
     * Import a document and convert it to enriched content blocks.
     *
     * Accepts either:
     * - A JSON body with 'markdown_content' (sent by JS after client-side docx→md conversion)
     * - A file upload (.md, .txt, .markdown)
     */
    public function importMarkdown(Request $request, Submission $submission)
    {
        set_time_limit(300);

        // Accept either markdown content from client-side conversion or file upload
        if ($request->has('markdown_content')) {
            $markdown = $request->input('markdown_content');
        } else {
            $request->validate([
                'markdown_file' => 'required|file|max:5120',
            ]);

            $file = $request->file('markdown_file');
            $ext = strtolower($file->getClientOriginalExtension());

            if (!in_array($ext, ['md', 'txt', 'markdown'], true)) {
                return response()->json([
                    'error' => 'Format non supporté. Utilisez un fichier .md, .txt, .docx ou .odt.',
                ], 422);
            }

            $markdown = file_get_contents($file->getRealPath());
        }

        if (empty(trim($markdown))) {
            return response()->json(['error' => 'Le document est vide.'], 422);
        }

        try {
            $structured = app(DocumentConversionService::class)->enrichMarkdown($markdown);
            $blocks = app(MarkdownToBlocksService::class)->parse($structured['markdown']);
            $blocks = $this->enrichBlocksWithTaxonLinks($blocks, $structured['taxons']);

            $refs = is_array($structured['references']) ? $structured['references'] : [];
            $blocks = $this->enrichBlocksWithCitationTooltips($blocks, $refs);
            $affils = is_array($structured['authors_affiliations']) ? $structured['authors_affiliations'] : [];

            // Build display authors from affiliations: "Prénom NOM : affiliation" → "Prénom NOM"
            $authorNames = array_map(function ($a) {
                $parts = explode(':', $a, 2);
                return trim($parts[0]);
            }, $affils);
            $displayAuthors = implode(', ', array_filter($authorNames));

            return response()->json([
                'blocks' => $blocks,
                'count' => count($blocks),
                'references' => implode("\n", array_map('strval', $refs)),
                'authors_affiliations' => implode("\n", array_map('strval', $affils)),
                'display_authors' => $displayAuthors,
                'display_abstract' => (string) ($structured['abstract'] ?? ''),
                'display_summary' => (string) ($structured['summary'] ?? ''),
                'acknowledgements' => (string) ($structured['acknowledgements'] ?? ''),
                'detected_title' => (string) ($structured['title'] ?? ''),
                'title_en' => (string) ($structured['title_en'] ?? ''),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Update the submission title (PATCH from layout editor)
     */
    public function updateTitle(Request $request, Submission $submission)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
        ]);

        $submission->update(['title' => $validated['title']]);

        return response()->json(['success' => true]);
    }

    /**
     * Enrich paragraph blocks by wrapping taxon names with Artemisiae links.
     */
    private function enrichBlocksWithTaxonLinks(array $blocks, array $taxons): array
    {
        if (empty($taxons)) {
            return $blocks;
        }

        foreach ($blocks as &$block) {
            if ($block['type'] !== 'paragraph' || empty($block['content'])) {
                continue;
            }

            foreach ($taxons as $taxon) {
                $escaped = preg_quote($taxon, '/');
                $url = 'https://oreina.org/artemisiae/index.php?module=recherche&action=recherche&recherche=' . urlencode($taxon);

                $block['content'] = preg_replace(
                    '/<em>' . $escaped . '<\/em>(?![^<]*<\/a>)/u',
                    '<a href="' . $url . '" target="_blank"><em>' . htmlspecialchars($taxon, ENT_QUOTES) . '</em></a>',
                    $block['content']
                );
            }
        }

        return $blocks;
    }

    /**
     * Enrich inline citations with tooltip showing the full reference.
     * Matches patterns like (Author, 2023) or (Author & Author, 2023) or (Author et al., 2023)
     * and looks up the full reference from the references list.
     */
    private function enrichBlocksWithCitationTooltips(array $blocks, array $references): array
    {
        if (empty($references)) {
            return $blocks;
        }

        // Build a lookup: extract "Author(s), Year" patterns from full references
        $refLookup = [];
        foreach ($references as $ref) {
            // Match "Author(s) (year)" at the start of Harvard-style references
            if (preg_match('/^(.+?)\s*\((\d{4})\)/', $ref, $m)) {
                $key = mb_strtolower(trim($m[1]) . ', ' . $m[2]);
                $refLookup[$key] = $ref;
            }
        }

        foreach ($blocks as &$block) {
            if ($block['type'] !== 'paragraph' || empty($block['content'])) {
                continue;
            }

            // Match inline citations: (Something, 2023) or (Something & Something, 2023) etc.
            $block['content'] = preg_replace_callback(
                '/\(([^()]+?,\s*\d{4}[a-z]?)\)/',
                function ($match) use ($refLookup) {
                    $citation = $match[1]; // e.g. "Dupont & Martin, 2023"
                    $lookupKey = mb_strtolower(trim($citation));

                    // Try exact match first
                    $fullRef = $refLookup[$lookupKey] ?? null;

                    // Try fuzzy: match by year + first author surname
                    if (!$fullRef && preg_match('/(\w+).*,\s*(\d{4})/', $citation, $parts)) {
                        $surname = mb_strtolower($parts[1]);
                        $year = $parts[2];
                        foreach ($refLookup as $key => $ref) {
                            if (str_contains($key, $year) && str_contains(mb_strtolower($key), $surname)) {
                                $fullRef = $ref;
                                break;
                            }
                        }
                    }

                    if ($fullRef) {
                        $escaped = htmlspecialchars($fullRef, ENT_QUOTES, 'UTF-8');
                        return '<span class="cite" data-ref="' . $escaped . '">(' . $citation . ')</span>';
                    }

                    return $match[0]; // No match found, return unchanged
                },
                $block['content']
            );
        }

        return $blocks;
    }
}
