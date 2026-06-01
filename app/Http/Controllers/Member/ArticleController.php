<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Member;

class ArticleController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        if ($member && $member->isCurrentMember()) {
            $articles = Article::visibleToMember($member)->published()
                ->latest('published_at')
                ->paginate(12);
        } else {
            $articles = Article::publicOnly()->published()
                ->latest('published_at')
                ->paginate(12);
        }

        return view('member.articles.index', compact('articles', 'member'));
    }
}
