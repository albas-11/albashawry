<?php

if (!function_exists('__')) {

    /**
     * Helper pour récupérer une traduction depuis les fichiers messages.php
     *
     * @param string $key La clé de traduction (ex: 'Home', 'Add to Cart')
     * @param array $replace Tableau de valeurs à remplacer dans la traduction
     * @param string|null $locale Langue spécifique ('en' ou 'ar'), sinon utilise la langue active
     * @return string La traduction correspondante
     */
    function __(string $key, array $replace = [], ?string $locale = null)
    {
        return trans("messages.{$key}", $replace, $locale);
    }
}
