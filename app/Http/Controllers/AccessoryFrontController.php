<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\Brand;
use App\Services\WishlistService;
use Illuminate\Http\Request;

class AccessoryFrontController extends Controller
{
    protected $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function index(Request $request)
    {
        $query = Accessory::with(['primaryImage'])->where('is_active', true);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }


        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $accessories = $query->latest()->paginate(15);

        $types = ['case', 'cleaner', 'chain', 'cloth', 'repair kit'];

        return view('products.accessories', compact('accessories', 'types'));
    }

    public function show(Accessory $accessory)
    {
        abort_if(!$accessory->is_active, 404);

        $accessory->load('images');

        $relatedProducts = Accessory::where('is_active', 1)
            ->where('id', '!=', $accessory->id)
            ->inRandomOrder()
            ->limit(10)
            ->with('primaryImage')
            ->get();

        // ✅ Wishlist check
        $isInWishlist = false;
        if (auth()->check()) {
            $isInWishlist = $this->wishlistService->isInWishlist(auth()->id(), $accessory->id, 'accessory');
        }

        return view('products.accessories_show', compact('accessory', 'relatedProducts', 'isInWishlist'));
    }
}
