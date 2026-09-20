<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Get available payment methods for order
     */
    public function getAvailablePaymentMethods(Order $order)
    {
        return PaymentMethod::active()
            ->get()
            ->filter(function ($method) use ($order) {
                return $method->supportsAmount($order->total_amount)
                    && $method->supportsCurrency($order->currency ?? 'MAD');
            });
    }

    /**
     * Create payment record
     */
    public function createPayment(Order $order, PaymentMethod $paymentMethod, array $data = []): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $paymentMethod->id,
            'transaction_id' => $data['transaction_id'] ?? $this->generateTransactionId(),
            'amount' => $order->total_amount,
            'currency' => $order->currency ?? 'MAD',
            'status' => Payment::STATUS_PENDING,
            'payment_data' => $data['payment_data'] ?? null
        ]);
    }

    /**
     * Process payment based on method
     */
    public function processPayment(Payment $payment, array $data = [])
    {
        $paymentMethod = $payment->paymentMethod;

        switch ($paymentMethod->name) {
            case 'stripe':
                return $this->processStripePayment($payment, $data);

            case 'paypal':
                return $this->processPayPalPayment($payment, $data);

            case 'cash_on_delivery':
                return $this->processCashOnDelivery($payment);

            case 'bank_transfer':
                return $this->processBankTransfer($payment, $data);

            default:
                throw new \Exception("Unsupported payment method: {$paymentMethod->name}");
        }
    }

    /**
     * Process Stripe payment
     */
    protected function processStripePayment(Payment $payment, array $data)
    {
        // Implementation with Stripe SDK
        // This is a placeholder - implement with actual Stripe integration

        $payment->update([
            'status' => Payment::STATUS_PROCESSING,
            'payment_gateway_id' => $data['stripe_payment_intent_id'] ?? null
        ]);

        return [
            'success' => true,
            'requires_action' => false,
            'payment' => $payment
        ];
    }

    /**
     * Process PayPal payment
     */
    protected function processPayPalPayment(Payment $payment, array $data)
    {
        // Implementation with PayPal SDK
        // This is a placeholder

        $payment->update([
            'status' => Payment::STATUS_PROCESSING,
            'payment_gateway_id' => $data['paypal_order_id'] ?? null
        ]);

        return [
            'success' => true,
            'approval_url' => $data['approval_url'] ?? null,
            'payment' => $payment
        ];
    }

    /**
     * Process Cash on Delivery
     */
    protected function processCashOnDelivery(Payment $payment)
    {
        $payment->update([
            'status' => Payment::STATUS_PENDING
        ]);

        $payment->order->update([
            'payment_status' => 'pending',
            'payment_method' => 'cash_on_delivery'
        ]);

        return [
            'success' => true,
            'message' => __('Order placed successfully. Pay on delivery.'),
            'payment' => $payment
        ];
    }

    /**
     * Process Bank Transfer
     */
    protected function processBankTransfer(Payment $payment, array $data)
    {
        $payment->update([
            'status' => Payment::STATUS_PENDING,
            'payment_data' => [
                'bank_details' => $this->getBankDetails()
            ]
        ]);

        return [
            'success' => true,
            'message' => __('Please transfer to our bank account'),
            'bank_details' => $this->getBankDetails(),
            'payment' => $payment
        ];
    }

    /**
     * Handle payment webhook (Stripe, PayPal, etc.)
     */
    public function handleWebhook(string $provider, array $payload)
    {
        switch ($provider) {
            case 'stripe':
                return $this->handleStripeWebhook($payload);

            case 'paypal':
                return $this->handlePayPalWebhook($payload);

            default:
                throw new \Exception("Unsupported webhook provider: {$provider}");
        }
    }

    /**
     * Confirm payment success
     */
    public function confirmPayment(Payment $payment, string $transactionId = null): bool
    {
        $payment->markAsCompleted($transactionId);

        // Send confirmation email
        // Trigger order fulfillment

        return true;
    }

    /**
     * Refund payment
     */
    public function refundPayment(Payment $payment, float $amount = null): bool
    {
        $refundAmount = $amount ?? $payment->amount;

        if ($refundAmount > $payment->amount) {
            throw new \Exception('Refund amount cannot exceed payment amount');
        }

        // Process refund with payment gateway

        $isPartial = $refundAmount < $payment->amount;

        $payment->update([
            'status' => $isPartial ? Payment::STATUS_PARTIALLY_REFUNDED : Payment::STATUS_REFUNDED
        ]);

        $payment->order->update([
            'payment_status' => $isPartial ? 'partially_refunded' : 'refunded'
        ]);

        return true;
    }

    /**
     * Generate unique transaction ID
     */
    protected function generateTransactionId(): string
    {
        return 'TXN-' . strtoupper(Str::random(12)) . '-' . time();
    }

    /**
     * Get bank transfer details
     */
    protected function getBankDetails(): array
    {
        return [
            'bank_name' => 'Banque Populaire',
            'account_name' => 'Eyewear Shop',
            'account_number' => '1234567890',
            'rib' => '123456789012345678901234',
            'swift' => 'BCPOMAMC'
        ];
    }

    /**
     * Handle Stripe webhook
     */
    protected function handleStripeWebhook(array $payload)
    {
        // Implement Stripe webhook handling
        // Verify signature, process events
    }

    /**
     * Handle PayPal webhook
     */
    protected function handlePayPalWebhook(array $payload)
    {
        // Implement PayPal webhook handling
    }
}
