<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPrescription;
use App\Models\EyeExamBooking;
use App\Models\Store;
use App\Models\TelrTransaction;
use App\Services\TelrService;
use App\Services\OtoService;
use App\Services\CartService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected TelrService $telrService;
    protected OtoService $otoService;
    protected CartService $cartService;

    public function __construct(TelrService $telrService, OtoService $otoService, CartService $cartService)
    {
        $this->middleware('auth')->except(['telrCallback', 'telrCancel', 'telrDecline']);

        $this->telrService = $telrService;
        $this->otoService  = $otoService;
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getCartWithItems();

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $unit = (float) ($item['final_price'] ?? $item['price'] ?? 0);
            $qty  = (int) ($item['quantity'] ?? 1);
            $subtotal += $unit * $qty;
        }

        $appliedCoupon = session()->get('coupon', null);

        return view('checkout.index', compact('cart', 'subtotal', 'appliedCoupon'));
    }

    public function process(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'phone'          => 'required|string|max:20',
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'country'        => 'required|string|max:2',
            'zip'            => 'required|string|max:20',
            'payment_method' => 'required|string|in:cod,online',

            // shipping (online)
            'shipping_option_id' => 'nullable|string|max:100',
            'shipping_cost'      => 'nullable|numeric|min:0',
        ]);

        $cart = $this->cartService->getCartWithItems();
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Panier vide');
        }

        DB::beginTransaction();

        try {
            $subtotal = 0;
            foreach ($cart as $item) {
                $unit = (float) ($item['final_price'] ?? $item['price'] ?? 0);
                $qty  = (int) ($item['quantity'] ?? 1);
                $subtotal += $unit * $qty;
            }

            $shippingCost     = (float) ($request->input('shipping_cost', 0));
            $shippingOptionId = $request->input('shipping_option_id');

            $tax   = 0;
            $total = round($subtotal + $shippingCost, 2);

            $order = Order::create([
                'user_id'          => auth()->id(),
                'order_number'     => 'ORD-' . time(),

                'shipping_name'    => $validated['name'],
                'shipping_email'   => $validated['email'],
                'shipping_phone'   => $validated['phone'],
                'shipping_address' => $validated['address'],
                'shipping_city'    => $validated['city'],
                'shipping_state'   => null,
                'shipping_country' => $validated['country'],
                'shipping_zip'     => $validated['zip'],

                'subtotal'         => $subtotal,
                'discount'         => 0,
                'coupon_code'      => null,

                'shipping_option_id' => $shippingOptionId,
                'shipping_cost'      => $shippingCost,

                'tax'              => $tax,
                'total'            => $total,

                'payment_method'   => $validated['payment_method'],
                'status'           => 'pending',
                'payment_status'   => 'pending',
                'paid_at'          => null,
            ]);

            foreach ($cart as $item) {
                $unit = (float) ($item['final_price'] ?? $item['price'] ?? 0);
                $qty  = (int) ($item['quantity'] ?? 1);

                $orderItem = OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['id'],
                    'product_name' => $item['name'],
                    'product_sku'  => $item['sku'] ?? null,
                    'price'        => $unit,
                    'quantity'     => $qty,
                    'total'        => $unit * $qty,
                    'lens_type'    => $item['lens_type'] ?? null,
                ]);

                if (!empty($item['lens_type'])) {
                    $payload = json_decode($item['lens_type'], true);

                    if (json_last_error() === JSON_ERROR_NONE && is_array($payload)) {
                        if (($payload['flow'] ?? null) === 'add_prescription') {

                            $rx = $payload['prescription'] ?? [];
                            $od = $rx['od'] ?? [];
                            $os = $rx['os'] ?? [];

                            $rxFilePath = null;
                            $rxFileMime = null;

                            if (!empty($rx['upload_preview']) && is_string($rx['upload_preview'])) {
                                if (preg_match('/^data:(.*?);base64,(.*)$/', $rx['upload_preview'], $m)) {
                                    $rxFileMime = $m[1] ?? null;
                                    $b64 = $m[2] ?? null;

                                    if ($b64) {
                                        $ext = match ($rxFileMime) {
                                            'image/png'  => 'png',
                                            'image/jpeg' => 'jpg',
                                            'image/jpg'  => 'jpg',
                                            'application/pdf' => 'pdf',
                                            default => 'bin',
                                        };

                                        $fileName = 'rx/' . $order->id . '/' . Str::uuid() . '.' . $ext;
                                        Storage::disk('public')->put($fileName, base64_decode($b64));
                                        $rxFilePath = $fileName; // stored in public disk
                                    }
                                }
                            }

                            OrderPrescription::create([
                                'order_id'      => $order->id,
                                'order_item_id' => $orderItem->id,

                                'patient_name' => $rx['patient_name'] ?? null,

                                'od_sphere' => $od['sphere'] ?? null,
                                'od_cyl'    => $od['cyl'] ?? null,
                                'od_axis'   => $od['axis'] ?? null,

                                'os_sphere' => $os['sphere'] ?? null,
                                'os_cyl'    => $os['cyl'] ?? null,
                                'os_axis'   => $os['axis'] ?? null,

                                'pd'         => $rx['pd'] ?? null,
                                'pd_unknown' => (bool) ($rx['pd_unknown'] ?? false),

                                'rx_file_path' => $rxFilePath,
                                'rx_file_mime' => $rxFileMime ?? ($rx['upload_file_type'] ?? null),
                            ]);
                        }

                        if (in_array($payload['flow'] ?? null, ['book_eye_test', 'store_measurement'], true)) {
                            $this->syncEyeExamBooking($order, $orderItem, $payload);
                        }
                    }
                }
            }


            DB::commit();

            if ($order->payment_method === 'cod') {
                $order->update([
                    'status'         => 'processing',
                    'payment_status' => 'pending',
                    'paid_at'        => null,
                ]);

                if (!empty($order->shipping_option_id)) {
                    $ship = $this->otoService->createShipment($order->id, $order->shipping_option_id);
                    Log::info('OTO shipment result', $ship);
                }

                session()->forget('cart');

                return redirect()->route('checkout.success', $order->id)
                    ->with('success', 'Commande confirmée. Paiement à la livraison.');
            }

            $customer = (object)[
                'first_name'   => explode(' ', $order->shipping_name)[0] ?? 'Customer',
                'last_name'    => explode(' ', $order->shipping_name)[1] ?? '',
                'email'        => $order->shipping_email,
                'phone'        => $order->shipping_phone,
                'address'      => $order->shipping_address,
                'city'         => $order->shipping_city,
                'country_code' => $order->shipping_country,
            ];

            $result = $this->telrService->createPaymentRequest($order, $customer);

            if (!empty($result['success'])) {
                return redirect()->away($result['payment_url']);
            }

            return back()->with('error', $result['error'] ?? 'Erreur lors de la redirection vers Telr');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Checkout error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors du traitement de la commande.');
        }
    }


    public function telrCallback(Request $request)
    {
        $telrRef = $request->query('order_ref')
            ?? $request->query('ref')
            ?? $request->query('OrderRef');

        if (!$telrRef) {
            return redirect()->route('cart.index')->with('error', 'Telr reference missing.');
        }

        $transaction = TelrTransaction::where('telr_order_ref', $telrRef)->first();

        if (!$transaction) {
            Log::warning('TELR CALLBACK: transaction not found', ['ref' => $telrRef, 'query' => $request->query()]);
            return redirect()->route('cart.index')->with('error', 'Transaction introuvable.');
        }

        if ($transaction->status === 'paid') {
            $order = $transaction->order;
            return redirect()->route('checkout.success', $order->id)->with('success', 'Paiement déjà confirmé ✅');
        }

        $status = $this->telrService->checkPaymentStatus($telrRef);

        if (!empty($status['success']) && $this->telrService->isApproved($status)) {
            $transaction->update([
                'status' => 'paid',
                'response_data' => json_encode($status['data']),
            ]);

            $order = $transaction->order;
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'paid_at' => now(),
            ]);

            session()->forget('cart');

            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Paiement réussi !');
        }

        $transaction->update([
            'status' => 'failed',
            'response_data' => json_encode($status['data'] ?? $status),
        ]);

        $order = $transaction->order;
        $order->update([
            'payment_status' => 'failed',
            'status' => 'pending',
        ]);

        return redirect()->route('cart.index')
            ->with('error', 'Le paiement a échoué ou est en attente.');
    }

    public function telrCancel(Request $request)
    {
        return redirect()->route('cart.index')->with('error', 'Paiement annulé par l’utilisateur.');
    }

    public function telrDecline(Request $request)
    {
        return redirect()->route('cart.index')->with('error', 'Paiement refusé par la banque.');
    }

    public function success($orderId)
    {
        $order = Order::where('id', $orderId)->with('items.product')->firstOrFail();
        return view('checkout.success', compact('order'));
    }

    /**
     * Rattache le RDV (fait au moment de la confirmation dans la modale produit)
     * à la commande. Si aucun RDV n'a été enregistré en amont, on le crée ici
     * afin qu'il apparaisse toujours dans l'admin.
     */
    private function syncEyeExamBooking(Order $order, OrderItem $orderItem, array $payload): void
    {
        $schedule = $payload['schedule'] ?? [];
        $day      = $schedule['day'] ?? null;
        $slot     = $schedule['slot'] ?? null;

        if (!$day || !$slot) {
            return;
        }

        $storeId = $payload['store_id'] ?? null;
        $store   = $storeId ? Store::find($storeId) : null;

        $storeName = $store ? $store->name : ($payload['store']['name'] ?? null);
        if (is_array($storeName)) {
            $storeName = $storeName[app()->getLocale()] ?? (reset($storeName) ?: null);
        }

        $attributes = [
            'order_id'       => $order->id,
            'order_item_id'  => $orderItem->id,
            'user_id'        => $order->user_id,
            'store_id'       => $store?->id,
            'store_name'     => $storeName,
            'product_id'     => $orderItem->product_id,
            'product_name'   => $orderItem->product_name,
            'flow'           => $payload['flow'],
            'booking_date'   => $day,
            'booking_time'   => $slot,
            'status'         => 'confirmed',
        ];

        $bookingId = $payload['booking_id'] ?? null;
        $booking   = $bookingId ? EyeExamBooking::find($bookingId) : null;

        if ($booking) {
            $booking->update($attributes);
            return;
        }

        EyeExamBooking::create($attributes + [
            'customer_name'  => $payload['customer_name']  ?? $order->shipping_name,
            'customer_phone' => $payload['customer_phone'] ?? $order->shipping_phone,
        ]);
    }
}
