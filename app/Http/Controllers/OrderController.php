<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\Admin\NewOrderNotification;
use App\Notifications\Admin\OrderPaidNotification;
use App\Mail\Admin\NewOrderAdminMail;
use App\Mail\Admin\PaymentReceivedAdminMail;
use App\Mail\PaymentReceivedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Afficher les commandes du client
     */
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('account.orders.index', compact('orders'));
    }

    /**
     * Afficher une commande
     */
    public function show(Order $order)
    {
        // Vérifier que la commande appartient au client
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items');
        return view('account.orders.show', compact('order'));
    }

    /**
     * Créer une nouvelle commande
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Créer la commande
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => auth()->id(),
                'shipping_name' => $request->shipping_name,
                'shipping_email' => $request->shipping_email,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state ?? null,
                'shipping_zip' => $request->shipping_zip,
                'shipping_country' => $request->shipping_country ?? 'Maroc',
                'subtotal' => $request->subtotal,
                'discount' => $request->discount ?? 0,
                'coupon_code' => $request->coupon_code ?? null,
                'shipping_cost' => $request->shipping_cost ?? 0,
                'tax' => $request->tax ?? 0,
                'total' => $request->total,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes ?? null,
            ]);

            DB::commit();

            // NOTIFIER TOUS LES ADMINS
            $admins = User::whereHas('role', function($q) {
                $q->where('guard_name', 'admin');
            })->get();

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new NewOrderNotification($order));

                //ENVOYER EMAIL À TOUS LES ADMINS
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new NewOrderAdminMail($order));
                }
            }

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Commande créée avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Callback après paiement
     */
    public function paymentCallback(Request $request, Order $order)
    {
        try {
            // Vérifier que la commande appartient au client
            if ($order->user_id !== auth()->id()) {
                abort(403);
            }

            // Mettre à jour le statut de paiement
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now()
            ]);

            //ENVOYER EMAIL AU CLIENT
            Mail::to($order->shipping_email)->send(new PaymentReceivedMail($order));

            //NOTIFIER LES ADMINS
            $admins = User::whereHas('role', function($q) {
                $q->where('guard_name', 'admin');
            })->get();

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new OrderPaidNotification($order));

                //ENVOYER EMAIL AUX ADMINS
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new PaymentReceivedAdminMail($order));
                }
            }

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Paiement effectué avec succès');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur de paiement: ' . $e->getMessage());
        }
    }
}
