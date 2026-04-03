<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;

class CommunityController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        return view('member.community.index', compact('member'));
    }

    public function map()
    {
        return view('member.community.map');
    }
}
