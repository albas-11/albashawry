<?php

namespace App\Models;

use App\Models\Concerns\HasSeoFields;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasTranslations, HasSeoFields;

    /** Pages éditables depuis l'admin : slug => nom de la route publique */
    public const ROUTES = [
        'about' => 'about',
        'policy' => 'policy',
        'terms' => 'terms',
    ];

    public $translatable = ['title', 'content', 'seo_title', 'seo_description'];

    protected $fillable = [
        'slug',
        'title',
        'content',
        'is_active',
        'seo_title',
        'seo_description',
        'canonical_url',
        'seo_noindex',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'seo_noindex' => 'boolean',
        'title' => 'array',
        'content' => 'array',
        'seo_title' => 'array',
        'seo_description' => 'array',
    ];

    public function scopeSlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    /** URL publique de la page */
    public function getPublicUrlAttribute(): string
    {
        $route = self::ROUTES[$this->slug] ?? null;

        return $route ? route($route) : url('/');
    }

    /* ==================== SEO ==================== */

    public function seoTokens(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();

        return [
            '{Name}' => (string) $this->getTranslation('title', $locale, true),
            '{Title}' => (string) $this->getTranslation('title', $locale, true),
            // app.name est déjà alimenté par les réglages du site (AppServiceProvider)
            '{SiteName}' => (string) config('app.name'),
        ];
    }

    protected function seoFallbackTitle(string $locale): string
    {
        return (string) $this->getTranslation('title', $locale, true);
    }

    protected function seoFallbackDescription(string $locale): string
    {
        return (string) $this->getTranslation('content', $locale, true);
    }
}
