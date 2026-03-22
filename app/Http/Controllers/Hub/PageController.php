<?php

namespace App\Http\Controllers\Hub;

use App\Http\Controllers\Controller;
use App\Models\MembershipType;

class PageController extends Controller
{
    public function about()
    {
        return view('hub.pages.about');
    }

    public function contact()
    {
        return view('hub.pages.contact');
    }

    public function membership()
    {
        $membershipTypes = MembershipType::where('is_active', true)
            ->orderBy('price')
            ->get();

        return view('hub.pages.membership', compact('membershipTypes'));
    }
}
