<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();

        // Si c'est un admin, il a tous les droits
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Si c'est un user, vérifier ses permissions
        if ($user->role === 'user') {
            // Vérifier si l'utilisateur a la permission requise
            if (!$user->hasPermission($permission)) {
                abort(403, "You do not have permission: {$permission}");
            }

            return $next($request);
        }

        abort(403, 'Access denied.');
    }
}
