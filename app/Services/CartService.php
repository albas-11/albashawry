<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ContactLens;
use App\Models\Accessory;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected string $sessionKey = 'cart';

    public function getCart(): array
    {
        return Session::get($this->sessionKey, []);
    }

    public function getCartWithItems(): array
    {
        $cart = $this->getCart();
        $items = [];
        $locale = app()->getLocale();

        foreach ($cart as $key => $item) {
            $model = null;

            if ($item['type'] === 'product') {
                $model = Product::with('primaryImage')->find($item['id']);
            } elseif ($item['type'] === 'contact_lens') {
                $model = ContactLens::with('primaryImage')->find($item['id']);
            } elseif ($item['type'] === 'accessory') {
                $model = Accessory::with('primaryImage')->find($item['id']);
            }

            if (!$model) continue;

            $basePrice  = (float) ($model->discount_price ?? $model->price);
            $finalPrice = isset($item['price']) ? (float) $item['price'] : $basePrice; // ✅ final stored unit price

            $items[$key] = [
                'id'            => $model->id,
                'type'          => $item['type'],
                'name'          => $this->getTranslatedName($model, $locale),

                'price'         => $basePrice,
                'final_price'   => $finalPrice,

                'original_price' => (float) $model->price,
                'discount_price' => $model->discount_price ? (float) $model->discount_price : null,
                'has_discount'  => !empty($model->discount_price),

                'image'         => $this->resolveImage($model),
                'quantity'      => (int) ($item['quantity'] ?? 1),
                'sku'           => $model->sku ?? 'N/A',
                'stock'         => $model->stock ?? 999,

                'lens_type'     => $item['lens_type'] ?? null,
            ];
        }

        return $items;
    }

    private function resolveImage($model): string
    {
        if (!$model->primaryImage || empty($model->primaryImage->image)) {
            return 'images/placeholder.jpg';
        }

        $image = ltrim($model->primaryImage->image, '/');

        if (str_starts_with($image, 'images/')) return $image;
        return 'images/' . $image;
    }

    private function getTranslatedName($model, string $locale): string
    {
        if (!method_exists($model, 'getTranslations')) {
            return $model->name ?? '—';
        }

        $translations = $model->getTranslations('name');

        return $translations[$locale]
            ?? $translations['fr']
            ?? $translations['en']
            ?? collect($translations)->first()
            ?? '—';
    }

    public function addToCart(
        int $id,
        int $qty,
        string $type,
        ?string $lensType = null,
        float $price = null
    ): array {
        $cart = $this->getCart();

        $payloadHash = $lensType ? substr(md5($lensType), 0, 12) : null;
        $key = $type . '_' . $id . ($payloadHash ? '_' . $payloadHash : '');

        $cart[$key] = [
            'id'        => $id,
            'type'      => $type,
            'model'     => match ($type) {
                'product'      => Product::class,
                'contact_lens' => ContactLens::class,
                'accessory'    => Accessory::class,
            },
            'quantity'  => $qty,
            'lens_type' => $lensType,
            'price'     => $price,
        ];

        Session::put($this->sessionKey, $cart);
        Session::save();

        return $cart;
    }

    public function updateQuantity(string $key, int $qty): array
    {
        $cart = $this->getCart();

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = max(1, $qty);
            Session::put($this->sessionKey, $cart);
            Session::save();
        }

        return $cart;
    }

    public function removeFromCart(string $key): array
    {
        $cart = $this->getCart();
        unset($cart[$key]);

        Session::put($this->sessionKey, $cart);
        Session::save();

        return $cart;
    }

    public function clearCart(): void
    {
        Session::forget($this->sessionKey);
        Session::save();
    }

    public function getTotal(): float
    {
        $cartItems = $this->getCartWithItems();
        $total = 0;

        foreach ($cartItems as $item) {
            $total += (float) $item['final_price'] * (int) $item['quantity'];
        }

        $coupon = $this->getAppliedCoupon();
        if ($coupon && isset($coupon['discount'])) {
            $total = $total * (1 - ($coupon['discount'] / 100));
        }

        return round($total, 2);
    }

    public function getCount()
    {
        return count($this->getCart());
    }

    public function getAppliedCoupon()
    {
        return Session::get('coupon');
    }

    public function applyCoupon($code)
    {
        if ($code === 'PROMO20') {
            $coupon = ['code' => $code, 'discount' => 20];
            Session::put('coupon', $coupon);
            Session::save();
            return 20;
        }
        throw new \Exception("Coupon invalide");
    }

    public function removeCoupon()
    {
        Session::forget('coupon');
        Session::save();
    }
}
