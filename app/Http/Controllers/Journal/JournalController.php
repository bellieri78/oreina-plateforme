<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\JournalIssue;
use App\Models\Submission;

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

    public function showArticle(Submission $submission)
    {
        if ($submission->status !== 'published') {
            abort(404);
        }

        $submission->load(['author', 'journalIssue']);

        $relatedArticles = Submission::published()
            ->where('id', '!=', $submission->id)
            ->where('journal_issue_id', $submission->journal_issue_id)
            ->with(['author', 'journalIssue'])
            ->take(3)
            ->get();

        return view('journal.articles.show', compact('submission', 'relatedArticles'));
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
