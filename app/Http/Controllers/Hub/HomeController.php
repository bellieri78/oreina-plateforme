<?php

namespace App\Http\Controllers\Hub;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        $featuredArticles = Article::published()
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(2)
            ->get();

        $latestArticles = Article::published()
            ->latest('published_at')
            ->take(4)
            ->get();

        $upcomingEvents = Event::published()
            ->upcoming()
            ->take(3)
            ->get();

        return view('hub.home', compact('featuredArticles', 'latestArticles', 'upcomingEvents'));
    }
}
