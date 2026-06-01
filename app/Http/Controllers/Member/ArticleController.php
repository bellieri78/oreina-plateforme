<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Member;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $member = Member::where('user_id', auth()->id())->first();

        $base = ($member && $member->isCurrentMember())
            ? Article::visibleToMember($member)
            : Article::publicOnly();

        $query = $base->published()->with('author');

        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }

        $articles = $query->latest('published_at')->paginate(12)->withQueryString();

        $categories = Article::whereNotNull('category')->distinct()->pluck('category')->sort();

        return view('member.articles.index', compact('articles', 'categories'));
    }
}
