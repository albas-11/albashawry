<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Mail\OrderConfirmedMail;
use App\Mail\OrderShippedMail;
use App\Mail\OrderDeliveredMail;
use App\Mail\OrderCancelledMail;
use App\Services\OtoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class OrderController extends Controller
{
    /**
     * Afficher toutes les commandes
     */
    public function index(Request $request)
    {
        $query = Order::with('user')->orderBy('created_at', 'desc');

        // Recherche par order number ou nom client
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filtrage par client
        if ($request->filled('customer')) {
            $query->where('user_id', $request->customer);
        }

        $orders = $query->paginate(8);

        // Récupérer tous les clients pour le dropdown
        $customers = User::orderBy('name')->get();

        return view('admin.orders.index', compact('orders', 'customers'));
    }

    /**
     * Afficher une commande
     */
    public function show(Order $order, OtoService $otoService)
    {
        $order->load(['items.product', 'tapTransaction', 'otoShipment', 'user', 'customer']);

        $otoOptions = [];
        $otoError   = null;
        try {
            $fees = $otoService->checkDeliveryFeesForOrder($order);
            if ($fees['success']) {
                $otoOptions = $fees['options'];
            } else {
                $otoError = $fees['raw']['errorMsg'] ?? $fees['error'] ?? 'OTO error';
            }
        } catch (\Throwable $e) {
            $otoError = $e->getMessage();
        }

        return view('admin.orders.show', compact('order', 'otoOptions', 'otoError'));
    }

    /**
     * Mise à jour générale du statut
     */
    public function updateStatus(Request $request, Order $order)
    {
        // dd($request->all());
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return back();
        }

        try {
            $order->status = $newStatus;

            switch ($newStatus) {
                case 'confirmed':
                    $order->payment_status = 'paid';
                    break;
                case 'shipped':
                    $order->shipped_at = now();
                    break;
                case 'delivered':
                    $order->delivered_at = now();
                    break;
            }

            $order->save();
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }

        // Send email separately — never block the status update
        try {
            match ($newStatus) {
                'confirmed' => Mail::to($order->shipping_email)->send(new OrderConfirmedMail($order)),
                'shipped'   => Mail::to($order->shipping_email)->send(new OrderShippedMail($order)),
                'delivered' => Mail::to($order->shipping_email)->send(new OrderDeliveredMail($order)),
                'cancelled' => Mail::to($order->shipping_email)->send(new OrderCancelledMail($order)),
                default     => null,
            };
        } catch (\Exception $e) {
            Log::warning('Order email failed', ['order' => $order->id, 'error' => $e->getMessage()]);
        }

        return back()->with('success', __('Order status updated successfully'));
    }
    public function printInvoice(Order $order)
    {
        $order->load('items', 'user');

        return view('admin.orders.print_invoice', compact('order'));
    }


    /**
     * Approuver/Confirmer une commande
     */
    public function approve(Order $order)
    {
        try {
            $order->update(['status' => 'confirmed', 'payment_status' => 'paid']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

        try {
            Mail::to($order->shipping_email)->send(new OrderConfirmedMail($order));
        } catch (\Exception $e) {
            Log::warning('Order confirm email failed', ['order' => $order->id, 'error' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', __('Order confirmed and email sent to the customer'));
    }


    /**
     * Marquer comme expédiée
     */
    public function markAsShipped(Order $order)
    {
        try {
            $order->update(['status' => 'shipped', 'shipped_at' => now()]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

        try {
            Mail::to($order->shipping_email)->send(new OrderShippedMail($order));
        } catch (\Exception $e) {
            Log::warning('Order shipped email failed', ['order' => $order->id, 'error' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', __('Order shipped, email sent'));
    }

    public function markAsDelivered(Order $order)
    {
        try {
            $order->update(['status' => 'delivered', 'delivered_at' => now()]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

        try {
            Mail::to($order->shipping_email)->send(new OrderDeliveredMail($order));
        } catch (\Exception $e) {
            Log::warning('Order delivered email failed', ['order' => $order->id, 'error' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', __('Order delivered, email sent'));
    }

    public function cancel(Order $order)
    {
        try {
            $order->update(['status' => 'cancelled']);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

        try {
            Mail::to($order->shipping_email)->send(new OrderCancelledMail($order));
        } catch (\Exception $e) {
            Log::warning('Order cancel email failed', ['order' => $order->id, 'error' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', __('Order cancelled and email sent to the customer'));
    }

    public function destroy(Order $order)
    {
        try {
            $order->delete();
            return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.orders.index')->with('error', 'Could not delete order.');
        }
    }
}
