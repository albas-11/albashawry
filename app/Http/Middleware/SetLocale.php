<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = Session::get('locale', config('app.locale'));

        // Un visiteur sans session (dont Googlebot) doit tout de même obtenir une
        // langue supportée : sinon la page annonce lang="fr" alors que le contenu
        // est rendu via le fallback_locale.
        if (! in_array($locale, ['en', 'ar'])) {
            $locale = in_array(config('app.fallback_locale'), ['en', 'ar'])
                ? config('app.fallback_locale')
                : 'en';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
