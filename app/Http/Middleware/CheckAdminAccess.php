<?php
// app/Http/Middleware/CheckAdminAccess.php
// app/Http/Middleware/CheckAdminAccess.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin a toujours accès
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // User doit avoir au moins une permission
        if ($user->hasRole('user')) {
            if ($user->permissions()->count() > 0) {
                return $next($request);
            }
            return redirect()->route('no-access');
        }

        // Client n'a pas accès
        if ($user->hasRole('client')) {
            return redirect()->route('home')->with('error', 'Accès non autorisé');
        }

        abort(403, 'Accès non autorisé');
    }
}
