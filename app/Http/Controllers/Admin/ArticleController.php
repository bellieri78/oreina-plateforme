<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with('author');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('summary', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('author_id')) {
            $query->where('author_id', $request->get('author_id'));
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->get('is_featured') === '1');
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['created_at', 'published_at', 'title'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $articles = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Article::count(),
            'published' => Article::where('status', 'published')->count(),
            'pending' => Article::where('status', 'submitted')->count(),
            'draft' => Article::where('status', 'draft')->count(),
        ];

        $categories = Article::whereNotNull('category')->distinct()->pluck('category')->sort();
        $authors = User::whereHas('articles')->orderBy('name')->get();

        return view('admin.articles.index', compact('articles', 'stats', 'categories', 'authors'));
    }

    public function create()
    {
        $authors = User::orderBy('name')->get();
        return view('admin.articles.create', compact('authors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:draft,submitted,validated,published',
            'author_id' => 'nullable|exists:users,id',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if (empty($validated['author_id'])) {
            $validated['author_id'] = auth()->id();
        }

        $validated['is_featured'] = $request->boolean('is_featured');

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $article = Article::create($validated);

        return redirect()
            ->route('admin.articles.show', $article)
            ->with('success', 'Article cree avec succes.');
    }

    public function show(Article $article)
    {
        $article->load(['author', 'validator']);
        return view('admin.articles.show', compact('article'));
    }

    public function edit(Article $article)
    {
        $authors = User::orderBy('name')->get();
        return view('admin.articles.edit', compact('article', 'authors'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:draft,submitted,validated,published',
            'author_id' => 'nullable|exists:users,id',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'validation_notes' => 'nullable|string',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['is_featured'] = $request->boolean('is_featured');

        // Si on passe en validé ou publié
        if (in_array($validated['status'], ['validated', 'published']) && $article->status !== $validated['status']) {
            $validated['validated_by'] = auth()->id();
            $validated['validated_at'] = now();
        }

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return redirect()
            ->route('admin.articles.show', $article)
            ->with('success', 'Article mis a jour.');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', 'Article supprime.');
    }

    /**
     * Export articles to CSV
     */
    public function export(Request $request)
    {
        $query = Article::with('author');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('summary', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        if ($request->filled('ids')) {
            $ids = explode(',', $request->get('ids'));
            $query->whereIn('id', $ids);
        }

        $articles = $query->orderBy('created_at', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="articles_' . date('Y-m-d') . '.csv"',
        ];

        $columns = ['ID', 'Titre', 'Auteur', 'Categorie', 'Statut', 'Vedette', 'Date creation', 'Date publication'];

        $callback = function () use ($articles, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($articles as $a) {
                fputcsv($file, [
                    $a->id,
                    $a->title,
                    $a->author?->name ?? '-',
                    $a->category ?? '-',
                    $a->status,
                    $a->is_featured ? 'Oui' : 'Non',
                    $a->created_at->format('d/m/Y'),
                    $a->published_at?->format('d/m/Y') ?? '-',
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Bulk delete articles
     */
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|string']);

        $ids = explode(',', $request->get('ids'));
        $deleted = Article::whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.articles.index')
            ->with('success', "{$deleted} article(s) supprime(s).");
    }

    /**
     * Bulk update status
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|string',
            'status' => 'required|in:draft,submitted,validated,published',
        ]);

        $ids = explode(',', $request->get('ids'));
        $data = ['status' => $request->get('status')];

        if ($request->get('status') === 'published') {
            $data['published_at'] = now();
        }

        $updated = Article::whereIn('id', $ids)->update($data);

        return redirect()
            ->route('admin.articles.index')
            ->with('success', "{$updated} article(s) mis a jour.");
    }
}
