<?php

namespace App\Http\Controllers\Hub;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Member;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::published()
            ->publicOnly()
            ->with(['author'])
            ->latest('published_at')
            ->paginate(9);

        $categories = [
            'actualites' => 'Actualités',
            'observations' => 'Observations',
            'publications' => 'Publications',
            'conservation' => 'Conservation',
        ];

        return view('hub.articles.index', compact('articles', 'categories'));
    }

    public function show(Article $article)
    {
        $member = auth()->check()
            ? Member::where('user_id', auth()->id())->first()
            : null;

        abort_unless($article->isPublished() && $article->isVisibleToMember($member), 404);

        $article->increment('views_count');

        $relatedArticles = Article::published()
            ->publicOnly()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->with(['author'])
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('hub.articles.show', compact('article', 'relatedArticles'));
    }

    public function category(string $category)
    {
        $categories = [
            'actualites' => 'Actualités',
            'observations' => 'Observations',
            'publications' => 'Publications',
            'conservation' => 'Conservation',
        ];

        if (!isset($categories[$category])) {
            abort(404);
        }

        $articles = Article::published()
            ->publicOnly()
            ->where('category', $category)
            ->with(['author'])
            ->latest('published_at')
            ->paginate(9);

        $currentCategory = $categories[$category];

        return view('hub.articles.index', compact('articles', 'categories', 'category', 'currentCategory'));
    }
}
