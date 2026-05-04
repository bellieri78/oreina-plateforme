<?php

namespace App\Http\Middleware;

use App\Models\Member;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCurrentMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->id();
        if (!$userId) {
            return redirect('/connexion');
        }

        $member = Member::where('user_id', $userId)->first();

        if (!$member || !$member->isCurrentMember()) {
            return redirect()
                ->route('member.dashboard')
                ->with('error', 'Cet espace est réservé aux adhérents à jour de cotisation.');
        }

        $request->attributes->set('current_member', $member);

        return $next($request);
    }
}
