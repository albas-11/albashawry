<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accessory;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ContactLens;
use App\Models\TryonFile;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;


class ProductController extends Controller
{
    /* ==================== SEO ==================== */

    /** Règles de validation des champs SEO (tous optionnels) */
    private function seoRules(?int $ignoreId = null): array
    {
        return [
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[\pL\pN]+(?:-[\pL\pN]+)*$/u',
                Rule::unique('products', 'slug')->ignore($ignoreId),
            ],
            'seo_title_en' => 'nullable|string|max:255',
            'seo_title_ar' => 'nullable|string|max:255',
            'seo_description_en' => 'nullable|string|max:500',
            'seo_description_ar' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:255',
        ];
    }

    /** Champs SEO à enregistrer (hors slug, traité séparément) */
    private function seoPayload(Request $request): array
    {
        // On n'enregistre que les traductions réellement remplies :
        // une valeur vide fait retomber le SEO sur le nom / la description du produit.
        $onlyFilled = fn(array $translations) => array_filter($translations, fn($v) => filled($v));

        return [
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
        ];
    }

    /** Génère un slug unique, en suffixant si nécessaire */
    private function uniqueSlug(?string $value, ?int $ignoreId = null): ?string
    {
        // $language = null : pas de translittération ASCII, les slugs arabes sont conservés
        $base = Str::slug($value, '-', null);

        if ($base === '') {
            return null;
        }

        // Un slug purement numérique entrerait en conflit avec les URLs /products/{id}
        if (ctype_digit($base)) {
            $base .= '-p';
        }

        $slug = $base;
        $i = 2;

        while (
            Product::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /** 📋 Liste des produits avec filtres */

    private function applyFilters($query, Request $request, $type = 'product')
    {
        if ($request->search) {
            if ($type === 'product') {
                $query->where('name->en', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%');
            } else {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
        }

        if ($type === 'product') {
            if ($request->brand) {
                $query->where('brand_id', $request->brand);
            }
            if ($request->category) {
                $query->where('category_id', $request->category);
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $items = collect();

        /* ========= PRODUCTS ========= */
        if (!$request->type || $request->type === 'product') {
            $products = $this->applyFilters(Product::with(['category', 'brand', 'images']), $request, 'product')
                ->get()
                ->map(fn($item) => tap($item, fn(&$i) => $i->item_type = 'product'));

            $items = $items->merge($products);
        }

        /* ========= CONTACT LENSES ========= */
        if (!$request->type || $request->type === 'lens') {
            $lenses = $this->applyFilters(ContactLens::query(), $request, 'lens')
                ->get()
                ->map(fn($item) => tap($item, fn(&$i) => $i->item_type = 'lens'));

            $items = $items->merge($lenses);
        }

        /* ========= ACCESSORIES ========= */
        if (!$request->type || $request->type === 'accessory') {
            $accessories = $this->applyFilters(Accessory::query(), $request, 'accessory')
                ->get()
                ->map(fn($item) => tap($item, fn(&$i) => $i->item_type = 'accessory'));

            $items = $items->merge($accessories);
        }

        /* ========= PAGINATION ========= */
        $perPage = 12;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pagedItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $products = new LengthAwarePaginator(
            $pagedItems,
            $items->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::all(),
            'brands' => Brand::all(),
        ]);
    }


    /** ✏️ Formulaire de création */
    public function create()
    {
        return view('admin.products.create', [
            'brands' => Brand::where('is_active', true)->get(),
            'categories' => Category::where('is_active', true)->get(),
            'tryonFiles' => TryonFile::where('is_active', true)->get(),
        ]);
    }

    /** 💾 Enregistrement d’un produit */
    public function store(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'sku' => 'required|unique:products,sku',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|string',
            'gender' => 'nullable|string',
            'lens_shape' => 'nullable|string',
            'frame_color' => 'nullable|string',
            'frame_type' => 'nullable|string',
            'lens_size' => 'nullable|string',
            'arm_length' => 'nullable|string',
            'bridge_width' => 'nullable|string',
            'tryon_file_id' => 'nullable|exists:tryon_files,id',
            'images' => 'required|array|min:1',
            'images.*' => 'image|max:2048',
        ] + $this->seoRules(), [
            'discount_price.lt' => __('The discount price must be less than the original price.'),
            'slug.regex' => __('The URL may only contain letters, numbers and hyphens.'),
        ]);

        $product = Product::create([
            'name' => ['en' => $request->name_en, 'ar' => $request->name_ar],
            'sku' => $request->sku,
            'slug' => $this->uniqueSlug($request->slug ?: $request->name_en . '-' . $request->sku),
            'description' => ['en' => $request->description_en, 'ar' => $request->description_ar],
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock' => $request->stock,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'gender' => $request->gender,
            'lens_shape' => $request->lens_shape,
            'frame_color' => $request->frame_color,
            'frame_type' => $request->frame_type,
            'lens_size' => $request->lens_size,
            'arm_length' => $request->arm_length,
            'bridge_width' => $request->bridge_width,
            'tryon_available' => $request->boolean('tryon_available'),
            'tryon_file_id' => $request->tryon_file_id,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ] + $this->seoPayload($request));

        // Gestion des images avec nom basé sur le produit
        $baseName = preg_replace('/[^A-Za-z0-9\-]/', '_', $request->name_en) . $request->sku;
        foreach ($request->file('images') as $index => $image) {
            $extension = $image->getClientOriginalExtension();
            $filename = $baseName . ($index + 1) . '.' . $extension;

            $image->move(public_path('images/products'), $filename);

            $product->images()->create([
                'image' => 'images/products/' . $filename,
                'is_primary' => $index === 0,
                'order' => $index,
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', __('Product created successfully'));
    }

    /**  Formulaire d’édition */
    public function edit(Product $product)
    {
        $product->load('images');

        return view('admin.products.edit', [
            'product' => $product,
            'brands' => Brand::where('is_active', true)->get(),
            'categories' => Category::where('is_active', true)->get(),
            'tryonFiles' => TryonFile::where('is_active', true)->get(),
        ]);
    }

    /** Mise à jour d’un produit */
    public function update(Request $request, Product $product)
    {
        // dd($request->all());
        $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'sku' => 'required|unique:products,sku,' . $product->id,
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|string',
            'gender' => 'nullable|string',
            'lens_shape' => 'nullable|string',
            'frame_color' => 'nullable|string',
            'frame_type' => 'nullable|string',
            'lens_size' => 'nullable|string',
            'arm_length' => 'nullable|string',
            'bridge_width' => 'nullable|string',
            'tryon_file_id' => 'nullable|exists:tryon_files,id',
            'images.*' => 'image|max:2048',
        ] + $this->seoRules($product->id), [
            'discount_price.lt' => __('The discount price must be less than the original price.'),
            'slug.regex' => __('The URL may only contain letters, numbers and hyphens.'),
        ]);

        $product->update([
            'name' => ['en' => $request->name_en, 'ar' => $request->name_ar],
            'sku' => $request->sku,
            // Sur un produit existant, le slug n'est jamais généré automatiquement :
            // il reste NULL (URL /products/{id} inchangée) tant que l'admin ne le remplit pas.
            'slug' => filled($request->slug) ? $this->uniqueSlug($request->slug, $product->id) : null,
            'description' => ['en' => $request->description_en, 'ar' => $request->description_ar],
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock' => $request->stock,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'gender' => $request->gender,
            'lens_shape' => $request->lens_shape,
            'frame_color' => $request->frame_color,
            'frame_type' => $request->frame_type,
            'lens_size' => $request->lens_size,
            'arm_length' => $request->arm_length,
            'bridge_width' => $request->bridge_width,
            'tryon_available' => $request->boolean('tryon_available'),
            'tryon_file_id' => $request->tryon_file_id,
            'is_featured' => $request->boolean('is_featured'),
            'is_active' => $request->boolean('is_active'),
        ] + $this->seoPayload($request));

        if ($request->hasFile('images')) {
            $baseName = preg_replace('/[^A-Za-z0-9\-]/', '_', $request->name_en) . $request->sku;
            foreach ($request->file('images') as $index => $image) {
                $extension = $image->getClientOriginalExtension();
                $filename = $baseName . ($index + 1) . '.' . $extension;

                $image->move(public_path('images/products'), $filename);

                $product->images()->create([
                    'image' => 'images/products/' . $filename,
                ]);
            }
        }

        return back()->with('success', __('Product updated successfully'));
    }

    /**  Suppression d’un produit */
    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            if (file_exists(public_path($image->image))) {
                unlink(public_path($image->image));
            }
        }

        $product->delete();

        return back()->with('success', __('Product deleted successfully'));
    }

    /** Définir l’image principale */
    public function setPrimaryImage(Request $request, Product $product)
    {
        $request->validate([
            'image_id' => 'required|exists:images,id'
        ]);

        $product->images()->update(['is_primary' => false]);

        $product->images()
            ->where('id', $request->image_id)
            ->update(['is_primary' => true]);

        return response()->json(['success' => true]);
    }
}
