<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\ContactLens;
use App\Services\WishlistService;
use Illuminate\Http\Request;

class ContactLensFrontController extends Controller
{
    protected $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function index(Request $request)
    {
        $brands = Brand::where('is_active', 1)
            ->orderBy('name')
            ->get();

        $query = ContactLens::query()
            ->where('is_active', 1)
            ->with(['primaryImage', 'brand']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }
        if ($request->filled('color')) {
            $query->where('color', 'like', '%' . $request->color . '%');
        }
        if ($request->filled('convexity')) {
            $query->where('convexity', $request->convexity);
        }
        if ($request->filled('water_content')) {
            $query->where('water_content', $request->water_content);
        }

        $lenses = $query->latest()->paginate(10)->withQueryString();

        return view('products.contact-lenses', compact('lenses', 'brands'));
    }

    public function show(ContactLens $contactLens)
    {
        abort_if(!$contactLens->is_active, 404);

        $contactLens->load([
            'images',
            'brand',
            'reviews.user'   // <-- IMPORTANT
        ]);

        $relatedProducts = ContactLens::where('is_active', 1)
            ->where('id', '!=', $contactLens->id)
            ->inRandomOrder()
            ->limit(10)
            ->with('primaryImage')
            ->get();

        $isInWishlist = false;
        if (auth()->check()) {
            $isInWishlist = $this->wishlistService->isInWishlist(auth()->id(), $contactLens->id, 'contact_lens');
        }

        return view('products.show_lenses', compact('contactLens', 'relatedProducts', 'isInWishlist'));
    }
}
