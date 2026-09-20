<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Champs SEO partagés (titre, description, canonique, noindex).
 *
 * Le modèle doit :
 *  - déclarer 'seo_title' et 'seo_description' dans $translatable, $fillable et $casts ;
 *  - exposer une URL publique via getPublicUrlAttribute().
 */
trait HasSeoFields
{
    /**
     * Variables remplaçables dans les gabarits SEO.
     * Les modèles peuvent surcharger pour en ajouter ({Brand}, {SKU}...).
     */
    public function seoTokens(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();

        return [
            '{Name}' => (string) $this->getTranslation('name', $locale, true),
        ];
    }

    /** Remplace les variables {Name}, {Brand}... dans un gabarit SEO */
    public function renderSeoTemplate(?string $template, ?string $locale = null): string
    {
        if (blank($template)) {
            return '';
        }

        $tokens = $this->seoTokens($locale);

        return trim(str_ireplace(array_keys($tokens), array_values($tokens), $template));
    }

    /** Valeur de repli du titre quand aucun titre SEO n'est saisi */
    protected function seoFallbackTitle(string $locale): string
    {
        return (string) $this->getTranslation('name', $locale, true);
    }

    /** Valeur de repli de la description quand aucune description SEO n'est saisie */
    protected function seoFallbackDescription(string $locale): string
    {
        return (string) $this->getTranslation('description', $locale, true);
    }

    /** Titre <title> / og:title */
    public function metaTitle(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        // Pas de repli inter-langue sur les champs SEO : mieux vaut le titre du contenu
        // dans la bonne langue qu'un gabarit SEO rédigé dans l'autre langue.
        $title = $this->renderSeoTemplate($this->getTranslation('seo_title', $locale, false), $locale);

        return $title !== '' ? $title : $this->seoFallbackTitle($locale);
    }

    /** meta description, nettoyée du HTML et tronquée */
    public function metaDescription(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $description = $this->renderSeoTemplate($this->getTranslation('seo_description', $locale, false), $locale);

        if ($description === '') {
            $description = $this->seoFallbackDescription($locale);
        }

        $description = trim(preg_replace('/\s+/u', ' ', strip_tags($description)));

        return Str::limit($description, 160, '');
    }

    /** URL canonique : valeur saisie par l'admin, sinon l'URL publique */
    public function canonicalUrl(): string
    {
        return filled($this->canonical_url)
            ? $this->canonical_url
            : $this->public_url;
    }
}
