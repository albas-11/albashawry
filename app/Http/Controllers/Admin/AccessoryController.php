<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accessory;
use App\Models\Category;
use Illuminate\Http\Request;

class AccessoryController extends Controller
{
    public function index()
    {
        $accessories = Accessory::with('primaryImage')->paginate(20);
        return view('admin.accessories.index', compact('accessories'));
    }

    public function create()
    {
        return view('admin.accessories.create', [
            'categories' => Category::where('is_active', true)->get(),
        ]);
    }

    public function edit($id)
    {
        $accessory = Accessory::findOrFail($id); // récupère l'accessoire
        return view('admin.accessories.edit', compact('accessory'));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name_en' => 'required|string',
            'name_ar' => 'required|string',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'type' => 'required|string',
        ]);

        // Création du modèle
        $accessory = new Accessory();

        // Traductions
        $accessory->setTranslations('name', [
            'en' => $request->input('name_en'),
            'ar' => $request->input('name_ar'),
        ]);

        $accessory->setTranslations('description', [
            'en' => $request->input('description_en'),
            'ar' => $request->input('description_ar'),
        ]);

        // Autres champs
        $accessory->price = $request->price;
        $accessory->stock = $request->stock;
        $accessory->type = $request->type;
        $accessory->is_active = $request->has('is_active') ? 1 : 0;

        $accessory->save();

        // Gestion des images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $filename = time() . '_' . $index . '_' . $image->getClientOriginalName();
                $image->move(public_path('images/accessories'), $filename);

                $accessory->images()->create([
                    'image' => 'images/accessories/' . $filename,
                    'is_primary' => $index === 0,
                    'order' => $index,
                ]);
            }
        }

      //  return back()->with('success', 'Accessory added successfully!');
        
     return redirect()->route('admin.products.index')
            ->with('success', __('Product created successfully'));
    }


    public function destroy(Accessory $accessory)
    {
        foreach ($accessory->images as $image) {
            if (file_exists(public_path($image->image))) {
                unlink(public_path($image->image));
            }
        }

        $accessory->delete();

        return back()->with('success', 'Accessory removed');
    }
}
