<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class TapService
{
    protected string $secretKey;
    protected string $baseUrl = 'https://api.tap.company/v2';

    public function __construct()
    {
        $settings = Setting::getInstance();
        $this->secretKey = $settings->tap_secret_key ?? env('TAP_SECRET_KEY', '');
    }

    public function createCharge($order, $customer)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'accept'        => 'application/json',
        ])->post($this->baseUrl . '/charges', [
            'amount'   => $order->total,
            'currency' => 'SAR', // يمكنك ربطها بالإعدادات
            'customer' => [
                'first_name' => $customer->first_name,
                'last_name'  => $customer->last_name,
                'email'      => $customer->email,
                'phone'      => [
                    'country_code' => '212', // قم بتعديله حسب الحاجة
                    'number'       => $customer->phone
                ]
            ],
            'source'   => ['id' => 'src_all'],
            'redirect' => ['url' => route('checkout.tap_callback')],
            'reference'=> ['transaction' => $order->order_number]
        ]);

        return $response->json();
    }

    public function getCharge($charge_id)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'accept'        => 'application/json',
        ])->get($this->baseUrl . '/charges/' . $charge_id);

        return $response->json();
    }
}