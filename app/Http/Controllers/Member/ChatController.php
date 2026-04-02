<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;

class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $member = Member::where('user_id', $user->id)->first();

        return view('member.chat.index', compact('member'));
    }
}
