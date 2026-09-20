<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ContactLens;
use App\Models\Accessory;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getCartWithItems();
        $total = $this->cartService->getTotal();
        $appliedCoupon = $this->cartService->getAppliedCoupon();

        return view('cart.index', compact('cart', 'total', 'appliedCoupon'));
    }

    public function add(Request $request)
    {
        Log::info('ADD TO CART', $request->all());

        $request->validate([
            'item_id'     => 'required|integer',
            'item_type'   => 'required|in:product,contact_lens,accessory',
            'quantity'    => 'required|integer|min:1',
            'lens_type'   => 'nullable|string',
            'extra_price' => 'nullable|numeric|min:0',

            'rx_file'     => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            $model = match ($request->item_type) {
                'product'      => Product::class,
                'contact_lens' => ContactLens::class,
                'accessory'    => Accessory::class,
            };

            $item = $model::findOrFail($request->item_id);

            // Déterminer le prix de base
            $basePrice = $item->discount_price ?? $item->price;

            // Ajouter le supplément si fourni
            if ($request->has('extra_price') && $request->extra_price > 0) {
                $basePrice += $request->extra_price;
            }

            $lensType = $request->lens_type;

            if ($request->hasFile('rx_file') && $lensType) {
                $payload = json_decode($lensType, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($payload)) {
                    $path = $request->file('rx_file')->store('rx/tmp', 'public');

                    $payload['prescription']['rx_file_path'] = $path;
                    $payload['prescription']['rx_file_mime'] = $request->file('rx_file')->getMimeType();

                    unset($payload['prescription']['upload_preview']);

                    $lensType = json_encode($payload);
                }
            }

            $this->cartService->addToCart(
                $item->id,
                $request->quantity,
                $request->item_type,
                $request->lens_type ?? null,
                $basePrice
            );


            return response()->json([
                'success' => true,
                'message' => __('Product added to cart'),
                'count'   => $this->cartService->getCount(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->cartService->updateQuantity($request->cart_key, $request->quantity);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => $this->cartService->getTotal(),
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string',
        ]);

        $cart = $this->cartService->removeFromCart($request->cart_key);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => $this->cartService->getTotal(),
            'count' => $this->cartService->getCount(),
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        try {
            $discount = $this->cartService->applyCoupon($request->coupon_code);

            return response()->json([
                'success' => true,
                'message' => __('Coupon applied successfully'),
                'discount' => $discount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function removeCoupon()
    {
        $this->cartService->removeCoupon();

        return response()->json([
            'success' => true,
            'message' => __('Coupon removed'),
        ]);
    }
}
