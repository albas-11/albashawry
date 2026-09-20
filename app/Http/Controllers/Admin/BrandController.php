<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->paginate(10);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'is_active' => 'boolean',
        ]);

        $slug = Str::slug($request->name_en);

        // Logo
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoFile = $request->file('logo');
            $logoName = $logoFile->getClientOriginalName();
            $logoFile->move(public_path('images/brands'), $logoName);
            $logoPath = 'images/brands/' . $logoName;
        }

        // Banner
        $bannerPath = null;
        if ($request->hasFile('banner_image')) {
            $bannerFile = $request->file('banner_image');
            $bannerName = $bannerFile->getClientOriginalName();
            $bannerFile->move(public_path('images/brands'), $bannerName);
            $bannerPath = 'images/brands/' . $bannerName;
        }

        Brand::create([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'slug' => $slug,
            'logo' => $logoPath,
            'banner_image' => $bannerPath,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.brands.index')
            ->with('success', __('admin.Brand created successfully'));
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'is_active' => 'boolean',
        ]);

        $slug = Str::slug($request->name_en);

        // Logo
        if ($request->hasFile('logo')) {
            if ($brand->logo && file_exists(public_path($brand->logo))) {
                unlink(public_path($brand->logo));
            }
            $logoFile = $request->file('logo');
            $logoName = $logoFile->getClientOriginalName();
            $logoFile->move(public_path('images/brands'), $logoName);
            $brand->logo = 'images/brands/' . $logoName;
        }

        // Banner
        if ($request->hasFile('banner_image')) {
            if ($brand->banner_image && file_exists(public_path($brand->banner_image))) {
                unlink(public_path($brand->banner_image));
            }
            $bannerFile = $request->file('banner_image');
            $bannerName = $bannerFile->getClientOriginalName();
            $bannerFile->move(public_path('images/brands'), $bannerName);
            $brand->banner_image = 'images/brands/' . $bannerName;
        }

        $brand->update([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'slug' => $slug,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.brands.index')
            ->with('success', __('admin.Brand updated successfully'));
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->count() > 0) {
            return back()->with('error', __('admin.Cannot delete brand with existing products'));
        }

        if ($brand->logo && file_exists(public_path($brand->logo))) {
            unlink(public_path($brand->logo));
        }
        if ($brand->banner_image && file_exists(public_path($brand->banner_image))) {
            unlink(public_path($brand->banner_image));
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', __('admin.Brand deleted successfully'));
    }
}
