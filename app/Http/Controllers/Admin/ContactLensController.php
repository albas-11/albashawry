<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ContactLens;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactLensController extends Controller
{

    public function index()
    {
        $lenses = ContactLens::with('primaryImage')->paginate(20);
        return view('admin.products.index', compact('lenses'));
    }

    public function create()
    {
        return view('admin.contact-lenses.create', [
            'brands' => Brand::where('is_active', true)->get(),
            'categories' => Category::where('is_active', true)->get(),
        ]);
    }

    public function storeold3(Request $request)
    {
        // dd($request->all());
        $lens = ContactLens::create($request->except('images'));
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {

                $filename = time() . '_' . $index . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/lenses'), $filename);

                $lens->images()->create([
                    'image' => 'images/lenses/' . $filename,
                    'is_primary' => $index === 0,
                    'order' => $index,
                ]);
            }
        }

        return back()->with('success', __('Lens added'));
    }
    public function storeOld6(Request $request)
{
    $data = $request->except('images');

    // منع null/empty فـ duration_of_use (حيت العمود NOT NULL)
    $data['duration_of_use'] = trim((string)($data['duration_of_use'] ?? ''));
    if ($data['duration_of_use'] === '') {
        $data['duration_of_use'] = 'daily'; // default اختار اللي بغيتي
    }

    // checkbox كيقدر مايبعثش
    $data['is_active'] = $request->boolean('is_active');
    $data['is_featured'] = $request->boolean('is_featured');

    $lens = ContactLens::create($data);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $image) {
            $filename = time() . '_' . $index . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/lenses'), $filename);

            $lens->images()->create([
                'image' => 'images/lenses/' . $filename,
                'is_primary' => $index === 0,
                'order' => $index,
            ]);
        }
    }

     return redirect()->route('admin.products.index')
            ->with('success', __('Product created successfully'));
}

public function store(Request $request)
{
    // 1. Validate the data
    $validatedData = $request->validate([
        'name'             => 'required|array',      // Assuming translatable field based on your SQL error
        'name.ar'          => 'required|string',
        'name.en'          => 'nullable|string',     // Adjust language keys as needed

        'description'      => 'nullable|array',
        'description.ar'   => 'nullable|string',
        'description.en'   => 'nullable|string',

        'water_content'    => 'nullable|numeric',
        'diameter'         => 'nullable|numeric',
        'convexity'        => 'nullable|numeric',
        
        'price'            => 'required|numeric|min:0',  // <--- This fixes your error
        'discount_price'   => 'nullable|numeric|lt:price',
        
        'stock'            => 'required|integer|min:0',
        'type'             => 'required|string', 
        'duration_of_use'  => 'nullable|string',
        'color'            => 'nullable|string',
        
        'brand_id'         => 'required|exists:brands,id',
        'is_active'        => 'nullable|boolean',
        'is_featured'      => 'nullable|boolean',
        
        'images'           => 'nullable|array',
        'images.*'         => 'image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    // 2. Handle default values for optional fields
    if (empty($validatedData['duration_of_use'])) {
        $validatedData['duration_of_use'] = 'daily'; // Set your default here
    }

    // 3. Handle checkboxes (boolean casting)
    // Validate returns the values, but sometimes checkboxes are not sent if unchecked.
    // However, since we used 'boolean' validation rule or just merge:
    $validatedData['is_active'] = $request->boolean('is_active');
    $validatedData['is_featured'] = $request->boolean('is_featured');

    // 4. Create the Record
    $lens = ContactLens::create($validatedData);

    // 5. Handle Images
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $image) {
            $filename = time() . '_' . $index . '_' . $image->getClientOriginalName();
            
            // It is often better to use Storage facade, but keeping your move() logic:
            $image->move(public_path('images/lenses'), $filename);

            $lens->images()->create([
                'image'      => 'images/lenses/' . $filename,
                'is_primary' => $index === 0,
                'order'      => $index,
            ]);
        }
    }

    return redirect()->route('admin.products.index') // Adjusted route name based on typical conventions
            ->with('success', __('Product created successfully'));
}

    public function storeOLD(Request $request)
{
    $data = $request->validate([
        'name'            => 'required|array',
        'name.fr'         => 'required|string',
        'name.ar'         => 'required|string',

        'description'     => 'nullable|array',
        'description.fr'  => 'nullable|string',
        'description.ar'  => 'nullable|string',

        'water_content'   => 'nullable|numeric',
        'diameter'        => 'nullable|numeric',
        'convexity'       => 'nullable|numeric',
        'price'           => 'required|numeric',
        'discount_price'  => 'nullable|numeric',
        'stock'           => 'required|integer',
        'type'            => 'required|string|in:daily,monthly,yearly', // عدّل حسب عندك

        'duration_of_use' => 'required|string|in:daily,weekly,monthly,yearly', // مهم
        'color'           => 'nullable|string',
        'brand_id'        => 'required|integer|exists:brands,id',
        'is_active'       => 'nullable|boolean',

        'images'          => 'nullable|array',
        'images.*'        => 'image|max:4096',
    ]);

    // إذا checkbox ما كيبعثش القيمة
    $data['is_active'] = $request->boolean('is_active');

    $lens = ContactLens::create($data);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $image) {
            $filename = time().'_'.$index.'_'.$image->getClientOriginalName();
            $image->move(public_path('images/lenses'), $filename);

            $lens->images()->create([
                'image'      => 'images/lenses/'.$filename,
                'is_primary' => $index === 0,
                'order'      => $index,
            ]);
        }
    }

    return back()->with('success', __('Lens added'));
}

    public function edit(ContactLens $contact_lens)
    {
        $categories = Category::active()->get();
        $brands = Brand::where('is_active', 1)->get();

        return view('admin.contact-lenses.edit', [
            'contact_lens' => $contact_lens,
            'categories'   => $categories,
            'brands'       => $brands,
        ]);
    }
    

    public function update(Request $request, ContactLens $contact_lens)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric',
            'stock'       => 'nullable|integer',
            'brand_id'    => 'nullable|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'images.*'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $contact_lens->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('contact-lenses', 'public');
                $contact_lens->images()->create(['image' => $path]);
            }
        }

        return redirect()->route('admin.contact-lenses.edit', $contact_lens->id)
            ->with('success', __('Contact Lens updated successfully.'));
    }
    public function destroyImage(ContactLens $contact_lens, Image $image): JsonResponse
    {
        $img = $contact_lens->images()->whereKey($image->id)->first();

        if (!$img) {
            return response()->json(['message' => 'Not allowed'], 403);
        }

        $wasPrimary = (bool) $img->is_primary;

        // delete file from public/
        $fullPath = public_path($img->image);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        $img->delete();

        // if primary deleted -> pick a new one
        if ($wasPrimary) {
            $newPrimary = $contact_lens->images()->orderBy('order')->first();
            if ($newPrimary) {
                $newPrimary->update(['is_primary' => 1]);
            }
        }

        return response()->json(['message' => 'Deleted']);
    }


    public function destroy(ContactLens $contactLens)
    {
        foreach ($contactLens->images as $image) {
            if (file_exists(public_path($image->image))) {
                unlink(public_path($image->image));
            }
        }

        $contactLens->delete();

        return back()->with('success', __('Lens removed'));
    }
}
