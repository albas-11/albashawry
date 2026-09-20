<?php

use App\Models\Setting;

if (!function_exists('settings')) {
    /**
     * Get settings instance or specific setting value
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function settings($key = null, $default = null)
    {
        // Récupère l'instance unique de la table settings
        $settings = Setting::getInstance();

        if (is_null($key)) {
            return $settings;
        }

        return $settings->{$key} ?? $default;
    }
}

if (!function_exists('site_name')) {
    /**
     * Get site name
     */
    function site_name()
    {
        return settings('site_name', config('app.name'));
    }
}

if (!function_exists('site_logo')) {
    
    function site_logo()
    {
        $setting = \App\Models\Setting::first();
        return $setting ? $setting->logo : null;
    }
}

if (!function_exists('site_favicon')) {
    /**
     * Get site favicon URL
     */
    function site_favicon()
    {
        return settings()->favicon ?? null;
    }
}

if (!function_exists('shipping_cost')) {
    /**
     * Calculate shipping cost for order
     */
    function shipping_cost($orderTotal)
    {
        return settings()->getShippingCost($orderTotal);
    }
}

if (!function_exists('robots_directive')) {
    /**
     * Directive robots de la page courante.
     *
     * Les espaces privés (compte, panier, commande, authentification, admin)
     * n'ont aucune valeur en résultat de recherche et ne doivent pas être indexés.
     * Le filtrage se fait par nom de route : toute nouvelle route ajoutée dans
     * un de ces groupes est couverte automatiquement.
     *
     * @return string|null
     */
    function robots_directive()
    {
        $privateRoutes = [
            'account.*',
            'cart.*',
            'checkout.*',
            'wishlist.*',
            'orders.*',
            'my-orders',
            'reviews.*',
            'profile',
            'profile.*',
            'login',
            'login.*',
            'register',
            'logout',
            'password.*',
            'verification.*',
            'payment.*',
            'admin.*',
        ];

        if (request()->routeIs(...$privateRoutes)) {
            return 'noindex, nofollow';
        }

        // Recherche interne : pages à contenu variable/pauvre, jamais indexées.
        // « follow » pour que les liens produits restent explorés.
        if (request()->routeIs('products.search', 'home.filter')) {
            return 'noindex, follow';
        }

        // Navigation à facettes : une combinaison de filtres génère un nombre
        // quasi illimité d'URLs quasi dupliquées. Seules les pages « propres »
        // (catégorie, marque, type) restent indexables. La pagination (?page=)
        // n'est pas un filtre : elle reste indexable.
        foreach (seo_filter_params() as $param) {
            if (request()->filled($param)) {
                return 'noindex, follow';
            }
        }

        return null;
    }
}

if (!function_exists('seo_filter_params')) {
    /**
     * Paramètres de requête qui produisent une variante filtrée/triée d'une
     * page de listing déjà indexable.
     *
     * @return string[]
     */
    function seo_filter_params()
    {
        return [
            'brand',
            'category',
            'category_id',
            'gender',
            'shape',
            'type',
            'color',
            'lens_type',
            'convexity',
            'water_content',
            'min_price',
            'max_price',
            'sort',
            'search',
            'q',
        ];
    }
}

if (!function_exists('canonical_url')) {
    /**
     * URL canonique auto-référente et normalisée.
     *
     * Les paramètres de tracking sont retirés (ils ne changent pas le contenu)
     * ainsi que les paramètres vides et « page=1 ». Les autres paramètres sont
     * conservés et triés : une même page a ainsi toujours la même canonique,
     * quel que soit l'ordre des paramètres dans l'URL d'entrée.
     *
     * @return string
     */
    function canonical_url()
    {
        $tracking = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid', 'msclkid', 'ref'];

        $query = [];

        foreach (request()->query() as $key => $value) {
            if (in_array($key, $tracking, true) || is_array($value) || $value === '' || is_null($value)) {
                continue;
            }

            // page=1 est la même ressource que l'URL sans paramètre
            if ($key === 'page' && (int) $value <= 1) {
                continue;
            }

            $query[$key] = $value;
        }

        ksort($query);

        return $query
            ? url()->current() . '?' . http_build_query($query)
            : url()->current();
    }
}
