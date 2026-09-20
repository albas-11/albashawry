<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\ContactLens;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Top Brands
        $brands = Brand::where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        // Récupérer brand et category depuis la request
        $selectedCategoryId = $request->input('category');
        $selectedBrandId = $request->input('brand');

        // Si aucune brand sélectionnée, prendre la première
        if (!$selectedBrandId && $brands->count() > 0) {
            $selectedBrandId = $brands->first()->id;
        }

        // Products query
        $productsQuery = Product::with(['brand', 'primaryImage', 'category'])
            ->where('is_active', true);

        // Filtre par catégorie
        if ($selectedCategoryId && $selectedCategoryId !== 'all') {
            $productsQuery->where('category_id', $selectedCategoryId);
        }

        // Filtre par brand
        if ($selectedBrandId) {
            $productsQuery->where('brand_id', $selectedBrandId);
        }

        // Filtre search
        if ($request->filled('search')) {
            $productsQuery->where('name', 'like', '%' . $request->search . '%');
        }

        $filteredProducts = $productsQuery->latest()->limit(12)->get();

        // Banners
        $banners = Banner::where('is_active', true)
            ->where('position', 'hero')
            ->orderBy('order')
            ->get();

        $genders = ['male', 'female'];

        // All Categories
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('products')
            ->orderBy('order')
            ->get();

        // Featured Categories
        $featuredCategories = $categories->take(6);

        // Featured Products
        $featuredProducts = Product::with(['brand', 'primaryImage'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->limit(8)
            ->get();

        // Latest Products
        $latestProducts = Product::with(['brand', 'primaryImage'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Best Selling
        $bestSelling = Product::with(['brand', 'primaryImage'])
            ->where('is_active', true)
            ->orderBy('total_reviews', 'desc')
            ->limit(8)
            ->get();

        $filters = $request->only('brand');

        $shapes = ['rond', 'carré', 'rectangulaire', 'aviator'];

        $products = Product::where('is_active', 1)
            ->with('primaryImage')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Récupérer les Contact Lenses
        $contactLenses = ContactLens::with('primaryImage')
            ->where('is_active', true)
            ->latest()
            ->limit(12)
            ->get();
        return view('home', compact(
            'banners',
            'categories',
            'featuredCategories',
            'selectedCategoryId',
            'selectedBrandId',
            'filteredProducts',
            'featuredProducts',
            'latestProducts',
            'bestSelling',
            'brands',
            'filters',
            'genders',
            'shapes',
            'products',
            'contactLenses'
        ));
    }

    // AJAX endpoint for dynamic category filtering
    public function filterByCategory(Request $request)
    {
        $categoryId = $request->input('category_id');

        $query = Product::with(['brand', 'primaryImage', 'category'])
            ->where('is_active', true);

        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->limit(12)->get();

        return response()->json([
            'success' => true,
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'discount_price' => $product->discount_price,
                    'final_price' => $product->final_price,
                    'discount_percentage' => $product->discount_percentage,
                    'image' => $product->primaryImage ? asset('storage/' . $product->primaryImage->image) : null,
                    'brand' => $product->brand->name,
                    'category' => $product->category->name,
                    'rating' => $product->rating,
                    'total_reviews' => $product->total_reviews,
                    'tryon_available' => $product->tryon_available,
                    'url' => route('products.show', $product->seo_url),
                ];
            }),
            'count' => $products->count(),
        ]);
    }
}
