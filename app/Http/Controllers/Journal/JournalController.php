<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\JournalIssue;
use App\Models\Submission;
use App\Services\CitationExportService;
use Illuminate\Support\Str;

class JournalController extends Controller
{
    public function home()
    {
        $latestIssue = JournalIssue::published()
            ->latest('publication_date')
            ->first();

        $recentArticles = Submission::published()
            ->with(['author', 'journalIssue'])
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('journal.home', compact('latestIssue', 'recentArticles'));
    }

    public function articles()
    {
        $articles = Submission::published()
            ->with(['author', 'journalIssue'])
            ->latest('published_at')
            ->paginate(10);

        return view('journal.articles.index', compact('articles'));
    }

    public function showArticle(
        Submission $submission,
        \Illuminate\Http\Request $request,
        \App\Services\ArticleMetricsService $metrics,
        \App\Services\CrossrefCitationService $citations,
    ) {
        if (!$submission->isPublished()) {
            abort(404);
        }

        $submission->load(['author', 'journalIssue']);

        // Set the dedup cookie before recording so the event carries a stable cookie_id
        if (!$request->cookie('oreina_visitor')) {
            $cookieValue = (string) \Illuminate\Support\Str::uuid();
            $request->cookies->set('oreina_visitor', $cookieValue);
            \Illuminate\Support\Facades\Cookie::queue(
                'oreina_visitor',
                $cookieValue,
                60 * 24 * 365, // 1 year
                '/',
                null,
                config('session.secure', false),
                true, // httpOnly
                false,
                'lax'
            );
        }

        $metrics->recordView($submission, $request);

        if ($citations->shouldSync($submission)) {
            \App\Jobs\SyncCrossrefCitationsJob::dispatch($submission->id);
        }

        $relatedArticles = Submission::published()
            ->where('id', '!=', $submission->id)
            ->where('journal_issue_id', $submission->journal_issue_id)
            ->with(['author', 'journalIssue'])
            ->take(3)
            ->get();

        $articleMetrics = $metrics->getMetrics($submission);
        $toc = $this->buildToc($submission);

        return view('journal.articles.show', compact('submission', 'relatedArticles', 'articleMetrics', 'toc'));
    }

    private function buildToc(Submission $submission): array
    {
        // Seules les sections h2 figurent dans le TOC. Le compteur n'incrémente
        // que sur h2, en accord avec le Blade qui emet id="section-N" uniquement
        // pour les h2.
        $toc = [];
        $counter = 0;
        foreach ((array) $submission->content_blocks as $block) {
            if (($block['type'] ?? null) !== 'heading') {
                continue;
            }
            $level = ltrim((string) ($block['level'] ?? 'h2'), 'h');
            if ($level !== '2') {
                continue;
            }
            $counter++;
            $toc[] = [
                'number' => $counter,
                'label' => (string) ($block['content'] ?? ''),
                'anchor' => 'section-' . $counter,
            ];
        }
        return $toc;
    }

    public function issues()
    {
        $issues = JournalIssue::published()
            ->orderBy('volume_number', 'desc')
            ->orderBy('issue_number', 'desc')
            ->paginate(12);

        return view('journal.issues.index', compact('issues'));
    }

    public function showIssue(JournalIssue $issue)
    {
        if ($issue->status !== 'published') {
            abort(404);
        }

        $articles = $issue->submissions()
            ->where('status', 'published')
            ->with(['author'])
            ->orderBy('start_page')
            ->get();

        return view('journal.issues.show', compact('issue', 'articles'));
    }

    public function submit()
    {
        return view('journal.submit');
    }

    public function authors()
    {
        return view('journal.authors');
    }

    public function about()
    {
        return view('journal.about');
    }

    public function trackShare(
        Submission $submission,
        \Illuminate\Http\Request $request,
        \App\Services\ArticleMetricsService $metrics,
    ) {
        abort_unless($submission->isPublished(), 404);

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'network' => 'required|string|in:' . implode(',', \App\Models\ArticleEvent::NETWORKS),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $metrics->recordShare($submission, $request, $validator->validated()['network']);

        return response()->noContent();
    }

    public function downloadPdf(
        Submission $submission,
        \Illuminate\Http\Request $request,
        \App\Services\ArticleMetricsService $metrics,
    ) {
        abort_unless($submission->isPublished(), 404);
        abort_unless(!empty($submission->pdf_file), 404);

        $metrics->recordPdfDownload($submission, $request);

        return redirect(\Illuminate\Support\Facades\Storage::url($submission->pdf_file));
    }

    public function cite(Submission $submission, string $format, CitationExportService $citations)
    {
        abort_unless($submission->isPublished(), 404);

        $content = match ($format) {
            'bibtex' => $citations->toBibtex($submission),
            'ris'    => $citations->toRis($submission),
            default  => abort(404),
        };

        $contentType = match ($format) {
            'bibtex' => 'application/x-bibtex',
            'ris'    => 'application/x-research-info-systems',
        };

        $filename = Str::slug($submission->title) . '.' . ($format === 'bibtex' ? 'bib' : 'ris');

        return response($content)
            ->header('Content-Type', $contentType . '; charset=utf-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    public function search()
    {
        $query = request('q');

        $articles = Submission::published()
            ->with(['author', 'journalIssue'])
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('abstract', 'like', "%{$query}%")
                  ->orWhereJsonContains('keywords', $query);
            })
            ->latest('published_at')
            ->paginate(10);

        return view('journal.search', compact('articles', 'query'));
    }
}
