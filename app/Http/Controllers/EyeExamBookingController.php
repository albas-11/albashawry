<?php

namespace App\Http\Controllers;

use App\Models\EyeExamBooking;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;

class EyeExamBookingController extends Controller
{
    /**
     * Enregistre le RDV dès que le client confirme dans la modale produit,
     * pour qu'il apparaisse dans l'admin même si le panier est abandonné.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'  => ['required', 'string', 'min:3', 'max:120'],
            'customer_phone' => ['required', 'string', 'min:8', 'max:30'],
            'store_id'       => ['required', 'integer', 'exists:stores,id'],
            'booking_date'   => ['required', 'date_format:Y-m-d'],
            'booking_time'   => ['required', 'string', 'max:20'],
            'flow'           => ['nullable', 'string', 'in:book_eye_test,store_measurement'],
            'product_id'     => ['nullable', 'integer', 'exists:products,id'],
        ]);

        $store   = Store::find($validated['store_id']);
        $product = !empty($validated['product_id']) ? Product::find($validated['product_id']) : null;

        $booking = EyeExamBooking::create([
            'user_id'        => auth()->id(),
            'store_id'       => $store?->id,
            'store_name'     => $store ? $this->plainName($store->name) : null,
            'product_id'     => $product?->id,
            'product_name'   => $product ? $this->plainName($product->name) : null,
            'customer_name'  => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'flow'           => $validated['flow'] ?? 'book_eye_test',
            'booking_date'   => $validated['booking_date'],
            'booking_time'   => $validated['booking_time'],
            'status'         => 'pending',
        ]);

        return response()->json([
            'ok'         => true,
            'booking_id' => $booking->id,
        ]);
    }

    private function plainName($name): ?string
    {
        if (is_array($name)) {
            return $name[app()->getLocale()] ?? (reset($name) ?: null);
        }

        return $name;
    }
}
