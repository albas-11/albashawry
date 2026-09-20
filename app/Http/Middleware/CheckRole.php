<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!auth()->check()) {
            abort(403, 'Unauthenticated');
        }

        $user = auth()->user();

        // Vérifier si l'utilisateur a un des rôles requis
        if (!in_array($user->role, $roles)) {
            abort(403, 'Access denied. Insufficient role.');
        }

        return $next($request);
    }
}
