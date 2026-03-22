<?php

namespace App\Http\Controllers\Hub;

use App\Http\Controllers\Controller;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::published()
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
        if ($article->status !== 'published') {
            abort(404);
        }

        $article->increment('views_count');

        $relatedArticles = Article::published()
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
            ->where('category', $category)
            ->with(['author'])
            ->latest('published_at')
            ->paginate(9);

        $currentCategory = $categories[$category];

        return view('hub.articles.index', compact('articles', 'categories', 'category', 'currentCategory'));
    }
}
