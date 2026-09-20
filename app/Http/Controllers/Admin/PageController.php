<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Balises HTML autorisées dans le contenu des pages.
     * Volontairement restrictif : pas de <script>, <iframe>, <style>, ni attributs on*.
     */
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><s><h2><h3><h4><ul><ol><li><a><blockquote><hr><table><thead><tbody><tr><th><td><span><div><img><figure><figcaption>';

    /** Éléments supprimés avec leur contenu (et pas seulement leurs balises) */
    private const STRIPPED_ELEMENTS = ['script', 'style', 'iframe', 'object', 'embed', 'form'];

    /** 📋 Liste des pages éditables */
    public function index()
    {
        return view('admin.pages.index', [
            'pages' => Page::orderByRaw("FIELD(slug, 'about', 'policy', 'terms')")->get(),
        ]);
    }

    /** ✏️ Formulaire d'édition */
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    /** 💾 Mise à jour */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'content_en' => 'required|string',
            'content_ar' => 'required|string',
            'seo_title_en' => 'nullable|string|max:255',
            'seo_title_ar' => 'nullable|string|max:255',
            'seo_description_en' => 'nullable|string|max:500',
            'seo_description_ar' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:255',
        ]);

        $onlyFilled = fn(array $translations) => array_filter($translations, fn($v) => filled($v));

        $page->update([
            'title' => ['en' => $validated['title_en'], 'ar' => $validated['title_ar']],
            'content' => [
                'en' => $this->sanitize($validated['content_en']),
                'ar' => $this->sanitize($validated['content_ar']),
            ],
            'is_active' => $request->boolean('is_active'),
            'seo_title' => $onlyFilled([
                'en' => $request->seo_title_en,
                'ar' => $request->seo_title_ar,
            ]),
            'seo_description' => $onlyFilled([
                'en' => $request->seo_description_en,
                'ar' => $request->seo_description_ar,
            ]),
            'canonical_url' => $request->canonical_url ?: null,
            'seo_noindex' => $request->boolean('seo_noindex'),
        ]);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', __('admin.Page updated successfully'));
    }

    /**
     * Nettoie le HTML soumis : on ne garde qu'une liste blanche de balises,
     * puis on retire les attributs dangereux (on*, javascript:) des balises restantes.
     */
    private function sanitize(string $html): string
    {
        // Ces éléments sont retirés avec leur contenu, sinon strip_tags laisserait
        // le code JS/CSS visible sous forme de texte sur la page.
        foreach (self::STRIPPED_ELEMENTS as $tag) {
            $html = preg_replace('#<' . $tag . '\b[^>]*>.*?</' . $tag . '\s*>#is', '', $html);
            $html = preg_replace('#<' . $tag . '\b[^>]*/?>#i', '', $html);
        }

        $html = strip_tags($html, self::ALLOWED_TAGS);

        // Gestionnaires d'événements inline : onclick="...", onerror='...'
        $html = preg_replace('/\son[a-z-]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);

        // URLs javascript: / data: dans href et src
        $html = preg_replace('/\s(href|src)\s*=\s*("|\')\s*(javascript|data|vbscript):[^"\']*\2/i', '', $html);

        return trim($html);
    }
}
