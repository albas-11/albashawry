<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->latest()->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('admin.categories.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
            'order' => 'integer|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        Category::create([
            'name' => [
                'en' => $request->name_en,
                'ar' => $request->name_ar,
            ],
            'slug' => Str::slug($request->name_en),
            'description' => [
                'en' => $request->description_en,
                'ar' => $request->description_ar,
            ],
            'parent_id' => $request->parent_id,
            'image' => $imagePath,
            'is_active' => $request->boolean('is_active'),
            'order' => $request->order ?? 0,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category created successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $categories = Category::where('id', '!=', $category->id)->get();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, Category $category)
{
    // 1. التحقق من البيانات (تأكد من مطابقة الأسماء في الـ Blade)
    $request->validate([
        'name' => 'required|string|max:255', // إذا كان الحقل في الـ Blade باسم name
        'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'order' => 'nullable|integer',
    ]);

    // 2. تحضير مصفوفة البيانات
    // ملاحظة: إذا كنت تستخدم حقل واحد للاسم، سنقوم بتخزينه للغتين كمثال أو حسب رغبتك
    $data = [
        'name' => [
            'en' => $request->name, // القيمة القادمة من name في الـ Blade
            'ar' => $request->name, // يمكنك تعديل هذا إذا كان لديك حقلين في الواجهة
        ],
        'slug' => \Illuminate\Support\Str::slug($request->name),
        'description' => [
            'en' => $request->description ?? '',
            'ar' => $request->description ?? '',
        ],
        'parent_id' => $request->parent_id,
        'is_active' => $request->has('is_active'),
        'order' => $request->order ?? 0,
    ];

    // 3. معالجة الصورة (هنا يكمن الحل لظهور الصورة في الـ dump)
    if ($request->hasFile('image')) {
        // حذف الصورة القديمة إذا وجدت
        if ($category->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image);
        }
        
        // رفع الصورة الجديدة وتخزين المسار
        $path = $request->file('image')->store('categories', 'public');
        $data['image'] = $path; // إضافة المسار للمصفوفة التي ستحدث قاعدة البيانات
    }

    // 4. التحديث النهائي
    $category->update($data);

    return redirect()->route('admin.categories.index')
        ->with('success', __('Category updated successfully'));
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', __('Category deleted successfully'));
    }
}
