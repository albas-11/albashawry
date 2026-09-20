<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::where('is_active', 1)->orderBy('name')->get();

        return view('brands.index', compact('brands'));
    }
     public function show($slug)
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();

        $products = Product::where('brand_id', $brand->id)
            ->where('is_active', true)
                   ->paginate(10);

        return view('brands.show', compact('brand', 'products'));
    }
}
