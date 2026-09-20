<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Repositories\ProductRepository;
use App\Services\WishlistService;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    private $productRepo;
    private $wishlistService;

    public function __construct(ProductRepository $productRepo, WishlistService $wishlistService)
    {
        $this->productRepo = $productRepo;
        $this->wishlistService = $wishlistService;
    }

    public function index(Request $request)
    {
        $filters = [
            'brand' => $request->input('brand'),
            'category' => $request->input('category'),
            'gender' => $request->input('gender'),
            'shape' => $request->input('shape'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'search' => $request->input('search'),
            'sort' => $request->input('sort', 'newest'),
            'per_page' => 12,
        ];

        $products = $this->productRepo->filter($filters);

        $brands = Brand::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->whereNull('parent_id')->get();


        if ($request->filled('brand')) {
            $products->where('brand_id', $request->brand);
        }

        $shapes = ['round', 'square', 'aviator', 'cat-eye', 'rectangle', 'oval'];
        $genders = ['men', 'women', 'unisex', 'kids'];

        return view('products.index', compact('products', 'brands', 'categories', 'shapes', 'genders', 'filters'));
    }

    public function show($id)
    {
        // Accepte l'id (URLs historiques) ou le slug SEO
        $product = Product::with([
            'brand',
            'category',
            'primaryImage',
            'reviews' => function ($q) {
                $q->where('is_approved', true)->latest();
            },
            'reviews.user',
        ])
            ->when(
                is_numeric($id),
                fn($q) => $q->where('id', $id),
                fn($q) => $q->where('slug', $id)
            )
            ->firstOrFail();

        $product->incrementViews();

        $id = $product->id;
        $relatedProducts = $this->productRepo->getRelated($id, 4);

        $isInWishlist = false;
        if (auth()->check()) {
            $isInWishlist = $this->wishlistService->isInWishlist(auth()->id(), $id, 'product');
        }

        $reviews = $product->reviews()
            ->with('user')
            ->where('is_approved', true)
            ->latest()
            ->paginate(10);

        $brands = Brand::orderBy('name')->get();

        return view('products.show', compact('product', 'relatedProducts', 'isInWishlist', 'reviews', 'brands'));
    }


    public function search(Request $request)
    {
        $query = $request->input('q');
        $products = $this->productRepo->search($query);

        return view('products.search', compact('products', 'query'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = Product::with(['brand', 'primaryImage'])
            ->where('is_active', true)
            ->where('category_id', $category->id)
            ->paginate(10);

        return view('products.category', compact('category', 'products'));
    }

    public function brand($slug)
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();

        $products = Product::with(['brand', 'primaryImage'])
            ->where('is_active', true)
            ->where('brand_id', $brand->id)
            ->paginate(10);

        return view('products.brand', compact('brand', 'products'));
    }

    public function sunglasses(Request $request)
    {
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        // Commencer la requête de base pour les lunettes de soleil
        $query = Product::where('type', 'sunglasses') // ou adaptez selon votre structure
            ->with(['brand', 'category']);

        // Filtrer par recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('reference', 'LIKE', "%{$search}%");
            });
        }

        // Filtrer par marque
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Filtrer par catégorie
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtrer par genre
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Paginer les résultats
        $products = $query->latest()->paginate(10);

        return view('products.sunglasses', compact('products', 'brands', 'categories'));
    }


    public function eyeglasses(Request $request)
    {
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        $query = Product::where('type', 'eyeglasses')
            ->with(['brand', 'category']);

        // Filtrer par recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('reference', 'LIKE', "%{$search}%");
            });
        }

        // Filtrer par marque
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Filtrer par catégorie
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtrer par genre
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Paginer les résultats
        $products = $query->latest()->paginate(10);
        return view('products.eyeglasses', compact('products', 'brands', 'categories'));
    }



    public function clipons(Request $request)
    {
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        $query = Product::where('type', 'clipOns')
            ->with(['brand', 'category']);

        // Filtrer par recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('reference', 'LIKE', "%{$search}%");
            });
        }

        // Filtrer par marque
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // Filtrer par catégorie
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtrer par genre
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Paginer les résultats
        $products = $query->latest()->paginate(10);
        return view('products.clipon', compact('products', 'brands', 'categories'));
    }


    public function contactlenses(Request $request)
    {
        $products = Product::with(['brand', 'primaryImage', 'category'])
            ->where('is_active', true)
            ->whereHas('category', function ($query) {
                $query->where('slug', 'contactlenses');
            })
            ->paginate(10);

        return view('products.contact-lenses', compact('products'));
    }

    public function accessories(Request $request)
    {
        $products = Product::with(['primaryImage', 'category'])
            ->where('is_active', true)
            ->whereHas('category', function ($query) {
                $query->where('slug', 'accessories');
            })
            ->paginate(10);

        return view('products.accessories', compact('products'));
    }
}
