<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('admin.login');
        }

        $user = $request->user();
        $hasLepisAccess = $user->hasCapability(\App\Models\EditorialCapability::LEPIS_EDITOR);

        if (!$user->isAdmin() && !$user->isEditor() && !$hasLepisAccess) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}
