<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * List published articles with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 15), 50);

        $articles = Article::published()
            ->with('author:id,name')
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
            'links' => [
                'first' => $articles->url(1),
                'last' => $articles->url($articles->lastPage()),
                'prev' => $articles->previousPageUrl(),
                'next' => $articles->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Show a single article by slug
     */
    public function show(Article $article): JsonResponse
    {
        // Only show published articles
        if (!$article->isPublished()) {
            abort(404);
        }

        $article->load('author:id,name');

        return response()->json([
            'data' => $this->formatArticle($article, true),
        ]);
    }

    /**
     * Get articles by category
     */
    public function byCategory(Request $request, string $category): JsonResponse
    {
        $perPage = min($request->input('per_page', 15), 50);

        $articles = Article::published()
            ->where('category', $category)
            ->with('author:id,name')
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'data' => $articles->map(fn ($article) => $this->formatArticle($article)),
            'category' => $category,
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    /**
     * Format article for API response
     */
    private function formatArticle(Article $article, bool $includeContent = false): array
    {
        $data = [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'excerpt' => $article->excerpt,
            'category' => $article->category,
            'image' => $article->image ? asset('storage/' . $article->image) : null,
            'is_featured' => $article->is_featured,
            'published_at' => $article->published_at?->toISOString(),
            'author' => $article->author ? [
                'id' => $article->author->id,
                'name' => $article->author->name,
            ] : null,
        ];

        if ($includeContent) {
            $data['content'] = $article->content;
        }

        return $data;
    }
}
