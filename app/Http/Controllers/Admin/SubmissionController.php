<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalIssue;
use App\Models\Submission;
use App\Models\User;
use App\Services\ArticleLatexService;
use App\Services\ArticlePdfService;
use App\Services\CrossrefService;
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
        $authors = User::orderBy('name')->get();
        $issues = JournalIssue::orderBy('volume_number', 'desc')
            ->orderBy('issue_number', 'desc')
            ->get();

        $selectedIssue = $request->get('journal_issue_id');

        return view('admin.submissions.create', compact('authors', 'issues', 'selectedIssue'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'nullable|string',
            'keywords' => 'nullable|string|max:500',
            'author_id' => 'required|exists:users,id',
            'journal_issue_id' => 'nullable|exists:journal_issues,id',
            'status' => 'required|in:draft,submitted,under_initial_review,revision_requested,under_peer_review,revision_after_review,in_production,accepted,rejected,published',
            'editor_id' => 'nullable|exists:users,id',
            'editor_notes' => 'nullable|string',
            'doi' => 'nullable|string|max:255',
            'start_page' => 'nullable|integer|min:1',
            'end_page' => 'nullable|integer|min:1',
            'manuscript_file' => 'nullable|file|mimes:doc,docx,pdf,odt|max:20480',
            'pdf_file' => 'nullable|file|mimes:pdf|max:20480',
            'featured_image' => 'nullable|image|max:5120',
        ]);

        if ($validated['status'] === 'submitted' && empty($validated['submitted_at'])) {
            $validated['submitted_at'] = now();
        }

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        // Handle file uploads
        if ($request->hasFile('manuscript_file')) {
            $validated['manuscript_file'] = $request->file('manuscript_file')
                ->store('submissions/manuscripts', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            $validated['pdf_file'] = $request->file('pdf_file')
                ->store('submissions/pdfs', 'public');
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('submissions/images', 'public');
        }

        $submission = Submission::create($validated);

        return redirect()
            ->route('admin.submissions.show', $submission)
            ->with('success', 'Soumission creee avec succes.');
    }

    public function show(Submission $submission)
    {
        $submission->load(['author', 'editor', 'journalIssue', 'reviews.reviewer']);

        return view('admin.submissions.show', compact('submission'));
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
            'status' => 'required|in:draft,submitted,under_initial_review,revision_requested,under_peer_review,revision_after_review,in_production,accepted,rejected,published',
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
            'status' => 'required|in:draft,submitted,under_initial_review,revision_requested,under_peer_review,revision_after_review,in_production,accepted,rejected,published',
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

        return $latexService->stream($submission);
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
        if (!in_array($submission->status, [Submission::STATUS_ACCEPTED, Submission::STATUS_PUBLISHED])) {
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
        if (!in_array($submission->status, [Submission::STATUS_ACCEPTED, Submission::STATUS_PUBLISHED])) {
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
        if ($submission->status !== Submission::STATUS_ACCEPTED) {
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
            'status' => Submission::STATUS_PUBLISHED,
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

    /**
     * Show the layout editor for an article
     */
    public function layout(Submission $submission)
    {
        // Only allow layout for accepted or published articles
        if (!in_array($submission->status, [Submission::STATUS_ACCEPTED, Submission::STATUS_PUBLISHED])) {
            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('error', 'La maquette ne peut être éditée que pour les articles acceptés ou publiés.');
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
        if (!in_array($submission->status, [Submission::STATUS_ACCEPTED, Submission::STATUS_PUBLISHED])) {
            return redirect()
                ->route('admin.submissions.show', $submission)
                ->with('error', 'La maquette ne peut être éditée que pour les articles acceptés ou publiés.');
        }

        $validated = $request->validate([
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
}
