<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TelrService;
use App\Models\Order;
use App\Models\TelrTransaction;

class PaymentController extends Controller
{
    protected $telrService;

    public function __construct(TelrService $telrService)
    {
        $this->telrService = $telrService;
    }

    /**
     * Initier le paiement
     */
    public function initiatePayment(Request $request, $orderId)
    {
        $order = Order::with('customer')->findOrFail($orderId);

        // Vérifier que la commande n'est pas déjà payée
        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Cette commande est déjà payée');
        }

        // Créer la demande de paiement
        $result = $this->telrService->createPaymentRequest($order, $order->customer);

        if ($result['success']) {
            // Rediriger vers la page de paiement Telr
            return redirect($result['payment_url']);
        }

        return redirect()->route('checkout.index')
            ->with('error', $result['error']);
    }

    /**
     * Callback après paiement réussi
     */
    public function paymentCallback(Request $request)
    {
        $orderRef = $request->get('order_ref');

        if (!$orderRef) {
            return redirect()->route('home')->with('error', 'Référence de commande manquante');
        }

        // Trouver la transaction
        $transaction = TelrTransaction::where('telr_order_ref', $orderRef)->first();

        if (!$transaction) {
            return redirect()->route('home')->with('error', 'Transaction introuvable');
        }

        // Vérifier le statut auprès de Telr
        $result = $this->telrService->checkPaymentStatus($orderRef);

        if ($result['success'] && isset($result['status'])) {
            $status = $result['status'];

            if ($status === 'paid' || $status === '1' || $status === 1) {
                // Mettre à jour la transaction
                $transaction->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'response_data' => json_encode($result['data']),
                    'transaction_ref' => $result['transaction']['ref'] ?? null,
                    'payment_method' => $result['transaction']['type'] ?? null,
                    'card_last4' => $result['transaction']['card']['last4'] ?? null,
                ]);

                // Mettre à jour la commande
                $order = $transaction->order;
                $order->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'telr',
                    'paid_at' => now(),
                ]);

                return redirect()->route('orders.success', $order)
                    ->with('success', 'Paiement effectué avec succès !');
            } else {
                // Paiement non validé
                $transaction->update([
                    'status' => 'declined',
                    'response_data' => json_encode($result['data']),
                ]);

                return redirect()->route('orders.show', $transaction->order)
                    ->with('error', 'Le paiement n\'a pas été validé');
            }
        }

        return redirect()->route('home')->with('error', 'Erreur lors de la vérification du paiement');
    }

    /**
     * Callback si paiement annulé
     */
    public function paymentCancel(Request $request)
    {
        $orderRef = $request->get('order_ref');

        if ($orderRef) {
            $transaction = TelrTransaction::where('telr_order_ref', $orderRef)->first();
            if ($transaction) {
                $transaction->update(['status' => 'cancelled']);
            }
        }

        return redirect()->route('checkout.index')
            ->with('warning', 'Paiement annulé');
    }

    /**
     * Callback si paiement refusé
     */
    public function paymentDecline(Request $request)
    {
        $orderRef = $request->get('order_ref');

        if ($orderRef) {
            $transaction = TelrTransaction::where('telr_order_ref', $orderRef)->first();
            if ($transaction) {
                $transaction->update(['status' => 'declined']);
            }
        }

        return redirect()->route('checkout.index')
            ->with('error', 'Paiement refusé');
    }
}
