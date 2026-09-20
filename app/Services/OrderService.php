<?php
namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function createOrder(array $data, $userId)
    {
        return DB::transaction(function () use ($data, $userId) {
            $cart = $this->cartService->getCart();

            if (empty($cart)) {
                throw new \Exception('Cart is empty');
            }

            // Calculate totals
            $subtotal = $this->cartService->getTotal();
            $discount = 0;
            $couponCode = null;

            $appliedCoupon = $this->cartService->getAppliedCoupon();
            if ($appliedCoupon) {
                $discount = $appliedCoupon['discount'];
                $couponCode = $appliedCoupon['code'];
            }

            $shippingCost = $data['shipping_cost'] ?? 0;
            $tax = ($subtotal - $discount) * 0.15; // 15% tax
            $total = $subtotal - $discount + $shippingCost + $tax;

            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $userId,
                'shipping_name' => $data['name'],
                'shipping_email' => $data['email'],
                'shipping_phone' => $data['phone'],
                'shipping_address' => $data['address'],
                'shipping_city' => $data['city'],
                'shipping_state' => $data['state'] ?? null,
                'shipping_country' => $data['country'],
                'shipping_zip' => $data['zip'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'coupon_code' => $couponCode,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'total' => $total,
                'payment_method' => $data['payment_method'] ?? 'cod',
                'notes' => $data['notes'] ?? null,
            ]);

            // Create order items
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'product_sku' => $item['sku'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['price'] * $item['quantity'],
                ]);

                // Update product stock
                $product = Product::find($item['id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // Update coupon usage
            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)->first();
                if ($coupon) {
                    $coupon->increment('used_count');
                }
            }

            // Clear cart and coupon
            $this->cartService->clearCart();
            $this->cartService->removeCoupon();

            return $order;
        });
    }

    public function updateStatus($orderId, $status)
    {
        $order = Order::findOrFail($orderId);
        $order->status = $status;

        if ($status === 'shipped' && !$order->shipped_at) {
            $order->shipped_at = now();
        }

        if ($status === 'delivered' && !$order->delivered_at) {
            $order->delivered_at = now();
        }

        $order->save();

        return $order;
    }

    public function cancelOrder($orderId)
    {
        return DB::transaction(function () use ($orderId) {
            $order = Order::findOrFail($orderId);

            if ($order->status !== 'pending') {
                throw new \Exception('Cannot cancel order with status: ' . $order->status);
            }

            // Restore stock
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            $order->status = 'cancelled';
            $order->save();

            return $order;
        });
    }
}
?>
