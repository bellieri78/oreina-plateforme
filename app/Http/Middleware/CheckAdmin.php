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

        if (!$request->user()->isAdmin() && !$request->user()->isEditor()) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}
