<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\OtoService;
use App\Models\Order;
use App\Models\OtoShipment;
use App\Models\Setting;

class OtoShippingController extends Controller
{
    public function __construct(private OtoService $otoService)
    {
        //
    }

    public function calculateShipping(Request $request)
    {
        // Mode A: order_id موجود
        if ($request->filled('order_id')) {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
            ]);

            $order = Order::with(['customer', 'items'])->findOrFail($request->order_id);
            $result = $this->otoService->checkDeliveryFeesForOrder($order);

            return response()->json($result['success']
                ? ['success' => true, 'options' => $result['options']]
                : ['success' => false, 'error' => $result['error'], 'raw' => $result['raw'] ?? null]
            );
        }

        // Mode B: checkout mode (city/country)
        $validated = $request->validate([
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:2',
            'payment_method' => 'nullable|in:cod,online',
            'order_total' => 'nullable|numeric|min:0',
        ]);

        $totalDue = 0;
        if (($validated['payment_method'] ?? null) === 'cod') {
            $totalDue = (float)($validated['order_total'] ?? 0);
        }

        $settings = Setting::getInstance();

        $payload = [
            'weight'          => 1.0,
            'originCity'      => $settings->oto_warehouse_city ?? config('oto.default_pickup.city', 'Riyadh'),
            'destinationCity' => $validated['city'],
            'height'          => 10,
            'width'           => 10,
            'length'          => 10,
            'packageCount'    => 1,
            'totalDue'        => $totalDue,
        ];

        $result = $this->otoService->checkDeliveryFeesPayload($payload);

        return response()->json($result['success']
            ? ['success' => true, 'options' => $result['options']]
            : ['success' => false, 'error' => $result['error'], 'raw' => $result['raw'] ?? null]
        );
    }

    public function createShipment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'delivery_option_id' => 'required|string',
        ]);

        $deliveryOptionId = $validated['delivery_option_id'] ?? ($order->shipping_option_id ?? null);

        $result = $this->otoService->createShipment($order->id, $deliveryOptionId);

        if ($result['success']) {
            $msg = 'OTO shipment created successfully!';
            if ($result['label_url'] ?? null) {
                $msg .= ' Label ready.';
            } else {
                $msg .= ' Label will be available once OTO processes the shipment.';
            }
            return back()->with('success', $msg);
        }

        return back()->with('error', $result['error'] ?? 'OTO error');
    }

    public function saveOtoId(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        $request->validate(['oto_id' => 'required|string']);

        $shipment = $order->otoShipment;
        if (!$shipment) {
            return back()->with('error', 'No shipment record found for this order.');
        }

        $shipment->update([
            'oto_id' => $request->oto_id,
            'status' => 'confirmed',
        ]);

        // Try to fetch label now that we have otoId
        $label = $this->otoService->fetchLabel($request->oto_id, $order->id);

        if ($label['success'] ?? false) {
            return back()->with('success', 'OTO ID saved and label fetched successfully!');
        }

        return back()->with('success', 'OTO ID saved. Label will appear once OTO processes it (or download from OTO Dashboard).');
    }

    public function fetchLabel(Order $order): \Illuminate\Http\JsonResponse
    {
        $shipment = $order->otoShipment;

        // Even without a shipment record, try to fetch by order_number
        if ($shipment?->label_url) {
            return response()->json(['success' => true, 'label_url' => $shipment->label_url]);
        }

        // Use oto_id if available, otherwise fall back to order_number
        $otoId = $shipment?->oto_id ?? $order->order_number;

        $result = $this->otoService->fetchLabel($otoId, $order->id);

        return response()->json($result, $result['success'] ? 200 : 202);
    }

    /**
     * Reset the OTO shipment record so the admin can re-create from scratch
     */
    public function resetShipment(Order $order): \Illuminate\Http\RedirectResponse
    {
        $order->otoShipment?->delete();
        $order->update(['oto_order_id' => null]);

        return back()->with('success', 'تم إعادة تعيين بيانات OTO. يمكنك الآن إنشاء الشحنة من جديد.');
    }

    public function trackShipment(string $otoId)
    {
        $result = $this->otoService->trackShipment($otoId);

        if ($result['success']) {
            return view('shipping.track', [
                'tracking' => $result['tracking'],
                'oto_id' => $otoId,
            ]);
        }

        return back()->with('error', $result['error'] ?? 'Track failed');
    }

    public function cancelShipment(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|string',
            'shipment_id' => 'required|string',
        ]);

        $result = $this->otoService->cancelShipment($validated['order_id'], $validated['shipment_id']);

        return $result['success']
            ? back()->with('success', 'Expédition annulée')
            : back()->with('error', $result['error'] ?? 'Cancel failed');
    }

    //Webhook
    public function webhook(Request $request)
    {
        // Simple signature check
        if ($request->header('X-OTO-Signature') !== config('oto.webhook_secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        $otoId = $data['otoId'] ?? null;
        $status = $data['status'] ?? null;

        if ($otoId && $status) {
            $shipment = OtoShipment::where('oto_id', $otoId)->first();

            if ($shipment) {
                $shipment->update([
                    'status' => $this->mapOtoStatus($status),
                    'shipment_id' => $data['shipmentId'] ?? $shipment->shipment_id,
                    'carrier_name' => $data['carrierName'] ?? $shipment->carrier_name,
                ]);

                if (strtolower($status) === 'delivered') {
                    $shipment->order->update([
                        'shipping_status' => 'delivered',
                        'delivered_at' => now(),
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    protected function mapOtoStatus(string $status): string
    {
        $statusMap = [
            'confirmed' => 'confirmed',
            'picked_up' => 'picked_up',
            'in_transit' => 'in_transit',
            'out_for_delivery' => 'out_for_delivery',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled',
            'failed' => 'failed',
        ];

        return $statusMap[strtolower($status)] ?? 'in_transit';
    }
}
