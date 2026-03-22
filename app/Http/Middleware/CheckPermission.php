<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  $permission  Format: "module.action" (e.g., "members.view")
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Non authentifie');
        }

        // Parse permission string
        if (!str_contains($permission, '.')) {
            abort(403, 'Format de permission invalide');
        }

        [$module, $action] = explode('.', $permission, 2);

        if (!$user->hasPermission($module, $action)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Vous n\'avez pas la permission d\'effectuer cette action.',
                ], 403);
            }

            abort(403, 'Vous n\'avez pas la permission d\'effectuer cette action.');
        }

        return $next($request);
    }
}
