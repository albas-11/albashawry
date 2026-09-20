<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class TryonController extends Controller
{
    public function show($productId)
    {
        $product = Product::with('tryonFile')->findOrFail($productId);

        // Vérifier Try-On dispo
        if (!$product->tryon_available || !$product->tryonFile) {
            abort(404, 'Virtual Try-On not available');
        }

        return view('tryon.show', compact('product'));
    }
    public function getData($productId)
    {
        $product = Product::with('tryonFile')
            ->where('tryon_available', true)
            ->findOrFail($productId);

        if (!$product->tryonFile) {
            return response()->json(['error' => 'Try-on not available'], 404);
        }

        return response()->json([
            'success' => true,
            'tryon' => [
                'file_path' => asset('images/tryon/' . $product->tryonFile->file_path),
                'preview_image' => $product->tryonFile->preview_image
                    ? asset('images/tryon/' . $product->tryonFile->preview_image)
                    : null,
                'offset_x' => (float) $product->tryonFile->offset_x,
                'offset_y' => (float) $product->tryonFile->offset_y,
                'scale' => (float) $product->tryonFile->scale,
                'rotation' => (float) $product->tryonFile->rotation,
            ]
        ]);
    }
}
