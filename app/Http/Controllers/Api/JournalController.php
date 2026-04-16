<?php

namespace App\Http\Controllers\Api;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\JournalIssue;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JournalController extends Controller
{
    /**
     * List published journal issues
     */
    public function issues(Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 12), 50);

        $issues = JournalIssue::where('status', 'published')
            ->orderBy('volume_number', 'desc')
            ->orderBy('issue_number', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $issues->map(fn ($issue) => $this->formatIssue($issue)),
            'meta' => [
                'current_page' => $issues->currentPage(),
                'last_page' => $issues->lastPage(),
                'per_page' => $issues->perPage(),
                'total' => $issues->total(),
            ],
        ]);
    }

    /**
     * Show a single journal issue with its articles
     */
    public function showIssue(JournalIssue $issue): JsonResponse
    {
        if ($issue->status !== 'published') {
            abort(404);
        }

        $articles = $issue->submissions()
            ->where('status', Submission::STATUS_PUBLISHED)
            ->with(['author', 'journalIssue'])
            ->orderBy('start_page')
            ->get();

        return response()->json([
            'data' => array_merge(
                $this->formatIssue($issue),
                ['articles' => $articles->map(fn ($article) => $this->formatArticle($article))]
            ),
        ]);
    }

    /**
     * List published journal articles
     */
    public function articles(Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 15), 50);

        $articles = Submission::published()
            ->with(['author:id,name', 'journalIssue'])
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $articles->map(fn ($article) => $this->formatArticle($article)),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    /**
     * Show a single published article
     */
    public function showArticle(Submission $submission): JsonResponse
    {
        if (!$submission->isPublished()) {
            abort(404);
        }

        $submission->load(['author:id,name', 'journalIssue']);

        return response()->json([
            'data' => $this->formatArticle($submission, true),
        ]);
    }

    /**
     * Search articles
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $query = $request->input('q');
        $perPage = min($request->input('per_page', 15), 50);

        $articles = Submission::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('abstract', 'ilike', "%{$query}%")
                    ->orWhere('keywords', 'ilike', "%{$query}%");
            })
            ->with(['author:id,name', 'journalIssue'])
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'query' => $query,
            'data' => $articles->map(fn ($article) => $this->formatArticle($article)),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    /**
     * Get current user's submissions
     */
    public function mySubmissions(Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 20), 50);

        $submissions = Submission::where('author_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $submissions->map(fn ($s) => $this->formatSubmission($s)),
            'meta' => [
                'current_page' => $submissions->currentPage(),
                'last_page' => $submissions->lastPage(),
                'per_page' => $submissions->perPage(),
                'total' => $submissions->total(),
            ],
        ]);
    }

    /**
     * Submit a new article
     */
    public function submitArticle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string|min:100|max:3000',
            'keywords' => 'required|string|max:255',
            'manuscript_file' => 'required|file|mimes:pdf|max:20480',
            'co_authors' => 'nullable|array',
            'co_authors.*.name' => 'required|string|max:255',
            'co_authors.*.email' => 'nullable|email|max:255',
            'co_authors.*.affiliation' => 'nullable|string|max:255',
        ]);

        $manuscriptPath = $request->file('manuscript_file')
            ->store('submissions', 'public');

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

        return response()->json([
            'message' => 'Manuscrit soumis avec succès.',
            'data' => $this->formatSubmission($submission),
        ], 201);
    }

    /**
     * Show a specific submission (for the author)
     */
    public function showSubmission(Submission $submission): JsonResponse
    {
        if ($submission->author_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette soumission.');
        }

        return response()->json([
            'data' => $this->formatSubmission($submission, true),
        ]);
    }

    /**
     * Submit a revision
     */
    public function submitRevision(Request $request, Submission $submission): JsonResponse
    {
        if ($submission->author_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($submission->status, [SubmissionStatus::RevisionRequested, SubmissionStatus::RevisionAfterReview], true)) {
            return response()->json([
                'message' => 'Cette soumission ne peut pas être modifiée actuellement.',
            ], 422);
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

        $submission->update([
            'manuscript_file' => $manuscriptPath,
            'status' => $submission->status === SubmissionStatus::RevisionRequested
                ? SubmissionStatus::UnderInitialReview->value
                : SubmissionStatus::UnderPeerReview->value,
        ]);

        return response()->json([
            'message' => 'Révision soumise avec succès.',
            'data' => $this->formatSubmission($submission->fresh()),
        ]);
    }

    /**
     * Format issue for API response
     */
    private function formatIssue(JournalIssue $issue): array
    {
        return [
            'id' => $issue->id,
            'volume_number' => $issue->volume_number,
            'issue_number' => $issue->issue_number,
            'title' => $issue->title,
            'slug' => $issue->slug,
            'description' => $issue->description,
            'cover_image' => $issue->cover_image ? asset('storage/' . $issue->cover_image) : null,
            'pdf_file' => $issue->pdf_file ? asset('storage/' . $issue->pdf_file) : null,
            'publication_date' => $issue->publication_date?->toISOString(),
            'page_count' => $issue->page_count,
            'doi' => $issue->doi,
        ];
    }

    /**
     * Format article for API response
     */
    private function formatArticle(Submission $article, bool $includeAbstract = false): array
    {
        $data = [
            'id' => $article->id,
            'title' => $article->title,
            'keywords' => $article->keywords,
            'authors' => $this->formatAuthors($article),
            'doi' => $article->doi,
            'pages' => $article->page_range,
            'published_at' => $article->published_at?->toISOString(),
            'pdf_file' => $article->pdf_file ? asset('storage/' . $article->pdf_file) : null,
            'issue' => $article->journalIssue ? [
                'id' => $article->journalIssue->id,
                'title' => $article->journalIssue->title,
                'slug' => $article->journalIssue->slug,
            ] : null,
        ];

        if ($includeAbstract) {
            $data['abstract'] = $article->abstract;
            $data['citation'] = $article->citation;
        }

        return $data;
    }

    /**
     * Format submission for API response
     */
    private function formatSubmission(Submission $submission, bool $detailed = false): array
    {
        $data = [
            'id' => $submission->id,
            'title' => $submission->title,
            'status' => $submission->status,
            'status_label' => Submission::getStatuses()[$submission->status] ?? $submission->status,
            'decision' => $submission->decision,
            'decision_label' => $submission->decision ? (Submission::getDecisions()[$submission->decision] ?? $submission->decision) : null,
            'submitted_at' => $submission->submitted_at?->toISOString(),
            'created_at' => $submission->created_at?->toISOString(),
        ];

        if ($detailed) {
            $data['abstract'] = $submission->abstract;
            $data['keywords'] = $submission->keywords;
            $data['co_authors'] = $submission->co_authors;
            $data['editor_notes'] = $submission->editor_notes;
            $data['decision_at'] = $submission->decision_at?->toISOString();
            $data['manuscript_file'] = $submission->manuscript_file ? asset('storage/' . $submission->manuscript_file) : null;
        }

        return $data;
    }

    /**
     * Format authors string
     */
    private function formatAuthors(Submission $article): string
    {
        $authors = [$article->author?->name ?? 'Auteur inconnu'];

        if (!empty($article->co_authors)) {
            foreach ($article->co_authors as $coAuthor) {
                $authors[] = $coAuthor['name'];
            }
        }

        return implode(', ', $authors);
    }
}
