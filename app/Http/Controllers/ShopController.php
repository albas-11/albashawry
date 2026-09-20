<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where('is_active', true)
            ->with('primaryImage')
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->gender, fn ($q) => $q->where('gender', $request->gender))
            ->paginate(10);

        return view('shop.index', compact('products'));
    }

    public function show(Product $product)
    {
        abort_if(!$product->is_active, 404);

        $product->load('images', 'brand', 'category');

        $product->incrementViews();

        return view('shop.show', compact('product'));
    }
}
