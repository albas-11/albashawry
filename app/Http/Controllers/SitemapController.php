<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    private const PER_SITEMAP = 50000;

    public function index(): Response
    {
        $productCount = Product::query()
            ->where('is_active', true)
            ->where('seo_noindex', false)
            ->whereNotNull('slug')
            ->count();

        $productFiles = max(1, (int) ceil($productCount / self::PER_SITEMAP));

        $sitemaps = [
            url('/pages.xml'),
            url('/categories.xml'),
            url('/brands.xml'),
        ];

        for ($page = 1; $page <= $productFiles; $page++) {
            $sitemaps[] = url('/products-' . $page . '.xml');
        }

        return response()
            ->view('sitemap-xml.index', ['sitemaps' => $sitemaps])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function pages(): Response
    {
        $urls = [
            url('/'),
            url('/about-us'),
            url('/policy'),
            url('/terms-of-use'),
        ];

        return response()
            ->view('sitemap-xml.pages', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function categories(): Response
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('id')
            ->get(['id', 'slug', 'updated_at']);

        return response()
            ->view('sitemap-xml.categories', compact('categories'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function brands(): Response
    {
        $brands = Brand::query()
            ->where('is_active', true)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('id')
            ->get(['id', 'slug', 'updated_at']);

        return response()
            ->view('sitemap-xml.brands', compact('brands'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function products(int $page = 1): Response
    {
        abort_if($page < 1, 404);

        $products = Product::query()
            ->where('is_active', true)
            ->where('seo_noindex', false)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->orderBy('id')
            ->with('primaryImage')
            ->skip(($page - 1) * self::PER_SITEMAP)
            ->take(self::PER_SITEMAP)
            ->get(['id', 'slug', 'canonical_url', 'updated_at']);

        return response()
            ->view('sitemap-xml.products', compact('products'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
