<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\TelrTransaction;

class TelrService
{
    protected string $storeId;
    protected string $authKey;
    protected string $apiUrl;
    protected string $currency;
    protected string $mode;

    public function __construct()
    {
        $telr = config('services.telr', []);

        $this->storeId  = (string)($telr['store_id'] ?? '');
        $this->authKey  = (string)($telr['auth_key'] ?? '');
        $this->apiUrl   = (string)($telr['api_url'] ?? 'https://secure.telr.com/gateway/order.json');
        $this->currency = (string)($telr['currency'] ?? 'SAR');
        $this->mode     = (string)($telr['mode'] ?? 'test');

        if (!$this->storeId || !$this->authKey) {
            Log::error('TELR CONFIG MISSING', [
                'storeId' => $this->storeId ? 'set' : 'missing',
                'authKey' => $this->authKey ? 'set' : 'missing',
            ]);
        }
    }

    private function mapCountry(string $code): string
    {
        return match (strtoupper($code)) {
            'MA' => 'MAR',
            'AE' => 'ARE',
            'SA' => 'SAU',
            'EG' => 'EGY',
            'FR' => 'FRA',
            default => 'SAU',
        };
    }

    public function createPaymentRequest($order, $customer): array
    {
        Log::info('TELR SERVICE CALLED', ['order_id' => $order->id]);

        $cartId = 'ORDER-' . $order->id . '-' . time();

        $requestData = [
            'ivp_method'   => 'create',
            'ivp_store'    => $this->storeId,
            'ivp_authkey'  => $this->authKey,
            'ivp_cart'     => $cartId,
            'ivp_test'     => $this->mode === 'test' ? '1' : '0',
            'ivp_amount'   => number_format((float)$order->total, 2, '.', ''),
            'ivp_currency' => $this->currency,
            'ivp_desc'     => 'Commande #' . $order->order_number,
            'ivp_lang'     => 'en',

            'return_auth'  => route('checkout.telr.callback'),
            'return_can'   => route('checkout.telr.cancel'),
            'return_decl'  => route('checkout.telr.decline'),

            // Billing info
            'bill_fname'   => $customer->first_name ?? 'Customer',
            'bill_sname'   => !empty($customer->last_name) ? $customer->last_name : 'Customer',
            'bill_addr1'   => $customer->address ?? '',
            'bill_city'    => $customer->city ?? '',
            'bill_country' => $this->mapCountry($customer->country_code ?? 'SA'),
            'bill_email'   => $customer->email ?? '',
            'bill_phone'   => $customer->phone ?? '',
        ];

        try {
            $response = Http::timeout(30)
                ->asForm()
                ->post($this->apiUrl, $requestData);

            $data = $response->json();

            Log::info('TELR CREATE REQUEST', [
    'ivp_store' => $this->storeId ? 'set' : 'missing',
    'ivp_authkey' => $this->authKey ? 'set' : 'missing',
    'ivp_cart' => $cartId,
    'ivp_test' => $this->mode === 'test' ? '1' : '0',
    'ivp_amount' => number_format((float)$order->total, 2, '.', ''),
    'ivp_currency' => $this->currency,
    'return_auth' => route('checkout.telr.callback'),
]);

Log::info('TELR CREATE RESPONSE', [
    'status' => $response->status(),
    'body' => $response->body(),
    'json' => $data,
]);

            if ($response->successful() && isset($data['order']['ref'], $data['order']['url'])) {

                $transaction = TelrTransaction::create([
                    'order_id'        => $order->id,
                    'telr_order_ref'  => $data['order']['ref'],
                    'cart_id'         => $cartId,
                    'amount'          => (float)$order->total,
                    'currency'        => $this->currency,
                    'status'          => 'pending',
                    'response_data'   => json_encode($data),
                ]);

                return [
                    'success'         => true,
                    'payment_url'     => $data['order']['url'],
                    'order_ref'       => $data['order']['ref'],
                    'transaction_id'  => $transaction->id,
                ];
            }

            return [
                'success' => false,
                'error'   => $data['error']['message'] ?? 'Telr: réponse invalide',
                'raw'     => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('TELR EXCEPTION', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'error'   => 'Telr exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check status
     */
    public function checkPaymentStatus(string $telrOrderRef): array
    {
        $payload = [
            "method"  => "check",
            "store"   => (int) $this->storeId,
            "authkey" => $this->authKey,
            "order"   => ["ref" => $telrOrderRef],
        ];

        try {
            $response = Http::timeout(30)
                ->asJson()
                ->post($this->apiUrl, $payload);

            $data = $response->json();

            Log::info('TELR CHECK RESPONSE', $data ?? []);

            if ($response->successful() && isset($data['order'])) {
                return [
                    'success' => true,
                    'data'    => $data,
                ];
            }

            return [
                'success' => false,
                'error'   => $data['error']['message'] ?? 'Telr: erreur de vérification',
                'data'    => $data,
            ];
        } catch (\Throwable $e) {
            // Log::error('TELR CHECK EXCEPTION', ['message' => $e->getMessage()]);
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    public function isApproved(array $checkResult): bool
    {
        $data = $checkResult['data'] ?? [];

        $code = data_get($data, 'order.status.code')
            ?? data_get($data, 'order.status');

        $text = data_get($data, 'order.status.text');

        $code = is_string($code) ? strtoupper($code) : $code;
        $text = is_string($text) ? strtolower($text) : $text;

        if ($code === 'A') return true;
        if ($text === 'paid') return true;

        return false;
    }
}
