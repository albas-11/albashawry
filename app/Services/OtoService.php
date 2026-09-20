<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\OtoShipment;
use App\Models\Order;
use App\Models\Setting;

class OtoService
{
    protected ?string $clientId;
    protected ?string $clientSecret;
    protected string $apiUrl;
    protected ?string $token;

    const API_URL_LIVE = 'https://api.tryoto.com/rest/v2';
    const API_URL_TEST = 'https://staging-api.tryoto.com/rest/v2';

    public function __construct()
    {
        $settings = Setting::getInstance();

        $this->clientId     = $settings->oto_client_id ?: config('oto.client_id');
        $this->clientSecret = $settings->oto_client_secret ?: config('oto.refresh_token');
        $mode               = $settings->oto_mode ?? 'test';
        $this->apiUrl       = $mode === 'live' ? self::API_URL_LIVE : self::API_URL_TEST;

        $this->token = $this->getToken();
    }

    /**
     * Get access token from cache or authenticate
     */
    protected function getToken(): ?string
    {
        $cacheKey = 'oto_access_token_' . md5($this->clientId . $this->clientSecret);
        return Cache::remember($cacheKey, 55 * 60, function () {
            return $this->authenticate();
        });
    }

    /**
     * Authenticate with OTO using client_id / client_secret (refresh_token flow)
     */
    protected function authenticate(): ?string
    {
        try {
            if (!$this->clientSecret) {
                Log::error('OTO: client_secret (refresh_token) missing in settings');
                return null;
            }

            $http = Http::timeout(15)->withoutVerifying()->asJson();

            // OTO uses the client_secret as a refresh_token to get a short-lived access_token
            $response = $http->post($this->apiUrl . '/refreshToken', [
                'refresh_token' => $this->clientSecret,
            ]);

            if (!$response->successful()) {
                Log::error('OTO Auth HTTP Error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'url'    => $this->apiUrl . '/refreshToken',
                ]);
                return null;
            }

            $data = $response->json();
            $access = $data['access_token'] ?? null;

            if (!$access) {
                Log::error('OTO Auth Failed – access_token missing', ['data' => $data]);
                return null;
            }

            return $access;
        } catch (\Throwable $e) {
            Log::error('OTO Token Exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Flush token cache and re-authenticate (call after updating OTO settings)
     */
    public function refreshToken(): void
    {
        $cacheKey = 'oto_access_token_' . md5($this->clientId . $this->clientSecret);
        Cache::forget($cacheKey);
        $this->token = $this->authenticate();
    }

    /**
     * Shared HTTP client with Bearer token
     */
    protected function httpClient()
    {
        if (!$this->token) {
            throw new \Exception('OTO: no access token – check credentials in Settings');
        }

        $settings = Setting::getInstance();

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept'        => 'application/json',
            'X-Store-Name'  => $settings->oto_warehouse_name ?: config('oto.store_name', 'Store'),
        ])->asJson()->timeout(30)->withoutVerifying();
    }

    /**
     * Test the OTO connection – returns ['success', 'message', 'token']
     */
    public function testConnection(): array
    {
        if (!$this->clientSecret) {
            return [
                'success' => false,
                'message' => 'OTO client secret (refresh token) is not configured in Settings.',
            ];
        }

        $this->refreshToken();

        if ($this->token) {
            return [
                'success' => true,
                'message' => 'Connected to OTO successfully!',
                'token'   => substr($this->token, 0, 20) . '...',
                'mode'    => Setting::getInstance()->oto_mode ?? 'test',
            ];
        }

        return [
            'success' => false,
            'message' => 'OTO authentication failed – check client_id and client_secret.',
        ];
    }

    /**
     * Check delivery fees using a raw payload
     */
    public function checkDeliveryFeesPayload(array $payload): array
    {
        try {
            if (!$this->token) return ['success' => false, 'error' => 'OTO token missing'];

            $response = $this->httpClient()->post($this->apiUrl . '/checkOTODeliveryFee', $payload);
            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                $companies = $data['deliveryCompany'] ?? $data['data'] ?? [];
                $options   = [];

                foreach ($companies as $c) {
                    $id = (string) ($c['deliveryOptionId'] ?? $c['id'] ?? '');
                    if (!$id) continue;

                    $options[] = [
                        'id'            => $id,
                        'carrier_name'  => $c['deliveryOptionName'] ?? $c['deliveryCompanyName'] ?? 'Shipping',
                        'delivery_time' => $c['avgDeliveryTime'] ?? $c['deliveryTime'] ?? '',
                        'cost'          => (float) ($c['price'] ?? $c['cost'] ?? 0),
                        'logo'          => $c['logo'] ?? null,
                    ];
                }

                return ['success' => true, 'options' => $options, 'raw' => $data];
            }

            return [
                'success' => false,
                'error'   => $data['message'] ?? 'Cannot calculate delivery fees',
                'raw'     => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('OTO Delivery Fee Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check delivery fees for a specific order
     */
    public function checkDeliveryFeesForOrder(Order $order): array
    {
        $customer = $order->customer;
        $settings = Setting::getInstance();

        $payload = [
            'weight'          => (float) ($order->weight ?? 1.0),
            'originCity'      => $settings->oto_warehouse_city ?? config('oto.default_pickup.city', 'Riyadh'),
            'destinationCity' => $customer->city ?? $order->shipping_city,
            'height'          => (float) ($order->height ?? 10),
            'width'           => (float) ($order->width ?? 10),
            'length'          => (float) ($order->length ?? 10),
            'packageCount'    => 1,
            'totalDue'        => $order->payment_method === 'cod' ? (float) $order->total : 0,
        ];

        return $this->checkDeliveryFeesPayload($payload);
    }

    /**
     * Create order in OTO
     */
    public function createOrder($order): array
    {
        if (!$this->token) return ['success' => false, 'error' => 'OTO token missing'];

        if ($order->oto_order_id) {
            return ['success' => true, 'order_id' => $order->oto_order_id, 'message' => 'Already created'];
        }

        $order->loadMissing(['items', 'customer']);

        $settings = Setting::getInstance();
        $pickup   = [
            'code'    => $settings->oto_warehouse_code ?? config('oto.default_pickup.code', 'WH-01'),
            'name'    => $settings->oto_warehouse_name ?? config('oto.default_pickup.name', 'Main Warehouse'),
            'mobile'  => $settings->oto_warehouse_phone ?? config('oto.default_pickup.mobile'),
            'address' => $settings->oto_warehouse_address ?? config('oto.default_pickup.address'),
            'city'    => $settings->oto_warehouse_city ?? config('oto.default_pickup.city', 'Riyadh'),
            'country' => 'SA',
        ];

        $customer = $order->customer;

        $orderData = [
            'orderId'        => (string) $order->order_number,
            'createShipment' => false,
            'storeName'      => $settings->oto_warehouse_name ?? config('oto.store_name', 'Store'),

            'payment_method' => $order->payment_method === 'cod' ? 'cod' : 'paid',
            'amount'         => (float) $order->total,
            'amount_due'     => $order->payment_method === 'cod' ? (float) $order->total : 0,
            'shippingAmount' => (float) ($order->shipping_cost ?? 0),
            'subtotal'       => (float) ($order->subtotal ?? $order->total),
            'currency'       => $settings->currency ?? 'SAR',

            'packageSize'   => 'small',
            'packageCount'  => 1,
            'packageWeight' => (float) ($order->weight ?? 1.0),
            'boxWidth'      => (float) ($order->width ?? 10),
            'boxLength'     => (float) ($order->length ?? 10),
            'boxHeight'     => (float) ($order->height ?? 10),

            'orderDate'  => now()->format('d/m/Y H:i'),
            'senderName' => $pickup['name'],

            'senderInformation' => [
                'senderFullName'    => $pickup['name'] ?: 'Store',
                'senderMobile'      => preg_replace('/\s+/', '', $pickup['mobile'] ?? '0500000000'),
                'senderEmail'       => 'store@example.com',
                'senderCountry'     => $pickup['country'],
                'senderCity'        => $pickup['city'] ?: 'Riyadh',
                'senderAddressLine' => $pickup['address'] ?: ($pickup['city'] ?: 'Riyadh'),
            ],

            'customer' => [
                'name'     => $customer->full_name ?? $order->shipping_name ?? 'Customer',
                'email'    => $customer->email ?? $order->shipping_email ?? 'no-reply@example.com',
                'mobile'   => $customer->phone ?? $order->shipping_phone ?? '',
                'address'  => $customer->address ?? $order->shipping_address ?? '',
                'city'     => $customer->city ?? $order->shipping_city ?? 'Riyadh',
                'country'  => $customer->country_code ?? $order->shipping_country ?? 'SA',
                'postcode' => $customer->postal_code ?? $order->shipping_zip ?? '00000',
            ],

            'items' => $order->items->map(function ($item) {
                return [
                    'productId' => (string) ($item->product_id ?? $item->id),
                    'name'      => $item->product_name ?? $item->name,
                    'price'     => (float) ($item->price ?? 0),
                    'quantity'  => (float) ($item->quantity ?? 1),
                    'sku'       => $item->product_sku ?? $item->sku ?? 'N/A',
                    'rowTotal'  => (float) ($item->total ?? ((float) $item->price * (int) $item->quantity)),
                    'taxAmount' => (float) ($item->tax ?? 0),
                ];
            })->toArray(),
        ];

        try {
            Log::info('OTO createOrder payload', $orderData);

            $response = $this->httpClient()->post($this->apiUrl . '/createOrder', $orderData);
            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                $otoOrderId = $data['otoOrderId']
                    ?? $data['otoId']
                    ?? data_get($data, 'data.otoOrderId')
                    ?? data_get($data, 'data.otoId');

                if ($otoOrderId) {
                    $order->update(['oto_order_id' => $otoOrderId]);
                    return ['success' => true, 'order_id' => $otoOrderId, 'data' => $data];
                }
            }

            // OTO1063 = order already exists in OTO — treat as success
            if (($data['otoErrorCode'] ?? '') === 'OTO1063') {
                $otoOrderId = $data['orderId'] ?? $order->order_number;
                $order->update(['oto_order_id' => $otoOrderId]);
                return ['success' => true, 'order_id' => $otoOrderId, 'data' => $data];
            }

            Log::error('OTO createOrder failed', [
                'http_status' => $response->status(),
                'body'        => $response->body(),
            ]);

            return [
                'success' => false,
                'error'   => $data['errorMsg'] ?? $data['otoErrorMessage'] ?? 'OTO createOrder failed',
                'raw'     => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('OTO Create Order Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Create order + shipment in ONE call → returns otoId immediately
     */
    public function createShipment(int $orderId, ?string $deliveryOptionId = null): array
    {
        if (!$this->token) return ['success' => false, 'error' => 'OTO token missing'];
        if (!$deliveryOptionId) return ['success' => false, 'error' => 'deliveryOptionId is required'];

        $order    = Order::with(['customer', 'items'])->findOrFail($orderId);
        $settings = Setting::getInstance();
        $customer = $order->customer;

        $pickup = [
            'name'    => $settings->oto_warehouse_name ?: 'Store',
            'mobile'  => preg_replace('/\s+/', '', $settings->oto_warehouse_phone ?? '0500000000'),
            'city'    => $settings->oto_warehouse_city ?? 'Riyadh',
            'address' => $settings->oto_warehouse_address ?: ($settings->oto_warehouse_city ?? 'Riyadh'),
        ];

        // Single call: createOrder + createShipment = true → OTO returns otoId immediately
        $payload = [
            'orderId'          => (string) $order->order_number,
            'createShipment'   => true,
            'deliveryOptionId' => (int) $deliveryOptionId,
            'storeName'        => $settings->oto_warehouse_name ?? 'Store',

            'payment_method' => $order->payment_method === 'cod' ? 'cod' : 'paid',
            'amount'         => (float) $order->total,
            'amount_due'     => $order->payment_method === 'cod' ? (float) $order->total : 0,
            'shippingAmount' => (float) ($order->shipping_cost ?? 0),
            'subtotal'       => (float) ($order->subtotal ?? $order->total),
            'currency'       => $settings->currency ?? 'SAR',

            'packageSize'   => 'small',
            'packageCount'  => 1,
            'packageWeight' => (float) ($order->weight ?? 1.0),
            'boxWidth'      => (float) ($order->width ?? 10),
            'boxLength'     => (float) ($order->length ?? 10),
            'boxHeight'     => (float) ($order->height ?? 10),

            'orderDate'  => now()->format('d/m/Y H:i'),
            'senderName' => $pickup['name'],

            'senderInformation' => [
                'senderFullName'    => $pickup['name'],
                'senderMobile'      => $pickup['mobile'],
                'senderEmail'       => 'store@example.com',
                'senderCountry'     => 'SA',
                'senderCity'        => $pickup['city'],
                'senderAddressLine' => $pickup['address'],
            ],

            'customer' => [
                'name'     => $customer->full_name ?? $order->shipping_name ?? 'Customer',
                'email'    => $customer->email ?? $order->shipping_email ?? 'customer@example.com',
                'mobile'   => $customer->phone ?? $order->shipping_phone ?? '0500000000',
                'address'  => $customer->address ?? $order->shipping_address ?? 'Address',
                'city'     => $customer->city ?? $order->shipping_city ?? 'Riyadh',
                'country'  => 'SA',
                'postcode' => $customer->postal_code ?? $order->shipping_zip ?? '00000',
            ],

            'items' => $order->items->map(function ($item) {
                $price    = (float) ($item->price ?? 0);
                $qty      = (int) ($item->quantity ?? 1);
                $sku      = $item->product?->sku ?? $item->sku ?? ('P-' . ($item->product_id ?? $item->id));
                return [
                    'productId' => (string) ($item->product_id ?? $item->id),
                    'name'      => $item->product_name ?? $item->name ?? 'Product',
                    'price'     => $price,
                    'quantity'  => (float) $qty,
                    'sku'       => $sku,
                    'rowTotal'  => (float) ($item->total ?? ($price * $qty)),
                    'taxAmount' => 0.0,
                ];
            })->values()->toArray(),
        ];

        // OTO rejects empty items
        if (empty($payload['items'])) {
            $payload['items'] = [[
                'productId' => '1',
                'name'      => 'Eyewear',
                'price'     => (float) $order->total,
                'quantity'  => 1.0,
                'sku'       => 'GENERAL',
                'rowTotal'  => (float) $order->total,
                'taxAmount' => 0.0,
            ]];
        }

        try {
            Log::info('OTO createOrder+Shipment payload', ['orderId' => $payload['orderId'], 'items_count' => count($payload['items'])]);

            $response = $this->httpClient()->post($this->apiUrl . '/createOrder', $payload);
            $data     = $response->json();

            $alreadyExists = ($data['otoErrorCode'] ?? '') === 'OTO1063';

            // OTO1063: order already exists — shipment may or may not exist
            // Try a standalone createShipment call for the existing OTO order
            if ($alreadyExists) {
                Log::info('OTO1063: order exists, trying standalone createShipment', [
                    'orderId' => $order->order_number,
                ]);

                $shipPayload = [
                    'orderId'          => (string) $order->order_number,
                    'deliveryOptionId' => (int) $deliveryOptionId,
                ];

                $shipResponse = $this->httpClient()->post($this->apiUrl . '/createShipment', $shipPayload);
                $shipData     = $shipResponse->json();

                Log::info('OTO standalone createShipment response', [
                    'status' => $shipResponse->status(),
                    'body'   => $shipData,
                ]);

                if ($shipResponse->successful() && ($shipData['success'] ?? false)) {
                    // OTO confirmed the request (async) — poll orderStatus to get otoId + label
                    $statusData = $this->getOrderStatus($order->order_number);
                    Log::info('OTO orderStatus after createShipment', ['body' => $statusData]);

                    $otoId    = $shipData['otoId'] ?? $statusData['otoId'] ?? $statusData['orderId'] ?? null;
                    $labelUrl = $shipData['printAWBURL'] ?? $statusData['printAWBURL'] ?? null;

                    // Also try /print endpoint right away
                    if (!$labelUrl) {
                        $labelUrl = $this->tryFetchPrintLabel($order->order_number, $order->id);
                    }

                    OtoShipment::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'oto_id'             => $otoId,
                            'delivery_option_id' => $deliveryOptionId,
                            'status'             => $labelUrl ? 'confirmed' : 'processing',
                            'label_url'          => $labelUrl,
                            'weight'             => $order->weight ?? 1.0,
                            'shipping_cost'      => $order->shipping_cost ?? 0,
                            'currency'           => 'SAR',
                            'response_data'      => json_encode($statusData ?: $shipData),
                        ]
                    );

                    if ($otoId) {
                        $order->update(['oto_order_id' => $otoId]);
                    }

                    $mode = Setting::getInstance()->oto_mode ?? 'test';
                    $msg  = $labelUrl
                        ? null
                        : ($mode === 'test'
                            ? 'تم إرسال طلب الشحنة (وضع تجريبي — قد لا تتوفر البوليصة في sandbox). في الوضع الحقيقي ستظهر البوليصة مباشرة.'
                            : 'تم إرسال طلب الشحنة. انقر "تحميل البوليصة" بعد لحظة.');

                    return ['success' => true, 'oto_id' => $otoId, 'label_url' => $labelUrl, 'message' => $msg];
                }

                // Standalone createShipment failed — check orderStatus for existing label
                $statusData = $this->getOrderStatus($order->order_number);
                $labelUrl   = $statusData['printAWBURL'] ?? null;
                $otoId      = $statusData['otoId'] ?? $data['otoId'] ?? null;

                if ($labelUrl) {
                    OtoShipment::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'oto_id'        => $otoId,
                            'label_url'     => $labelUrl,
                            'status'        => 'confirmed',
                            'weight'        => $order->weight ?? 1.0,
                            'shipping_cost' => $order->shipping_cost ?? 0,
                            'currency'      => 'SAR',
                        ]
                    );
                    return ['success' => true, 'oto_id' => $otoId, 'label_url' => $labelUrl];
                }

                $shipError = $shipData['otoErrorMessage'] ?? $shipData['errorMsg'] ?? null;
                return [
                    'success' => false,
                    'error'   => $shipError ?? 'فشل إنشاء الشحنة في OTO. استخدم زر إعادة الإنشاء أو تواصل مع OTO.',
                    'raw'     => $shipData,
                ];
            }

            if ($response->successful() && ($data['success'] ?? false)) {
                $otoId    = $data['otoId'] ?? null;
                $labelUrl = $data['labelUrl'] ?? $data['label_url'] ?? $data['printAWBURL'] ?? null;

                OtoShipment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'oto_id'             => $otoId,
                        'delivery_option_id' => $deliveryOptionId,
                        'status'             => $otoId ? 'confirmed' : 'pending',
                        'label_url'          => $labelUrl,
                        'weight'             => $order->weight ?? 1.0,
                        'shipping_cost'      => $order->shipping_cost ?? 0,
                        'currency'           => 'SAR',
                        'response_data'      => json_encode($data),
                    ]
                );

                $order->update(['oto_order_id' => $otoId ?? $order->order_number]);

                if (!$labelUrl) {
                    $labelUrl = $this->tryFetchPrintLabel($order->order_number, $order->id);
                }

                return ['success' => true, 'oto_id' => $otoId, 'label_url' => $labelUrl];
            }

            Log::error('OTO createShipment failed', ['status' => $response->status(), 'body' => $response->body()]);

            return [
                'success' => false,
                'error'   => $data['errorMsg'] ?? $data['otoErrorMessage'] ?? 'OTO createShipment failed',
                'raw'     => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('OTO createShipment exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Helper: call GET /print/{orderId} and save label_url if found
     */
    protected function tryFetchPrintLabel(string $orderNumber, int $orderId): ?string
    {
        try {
            $res  = $this->httpClient()->get($this->apiUrl . '/print/' . $orderNumber);
            $data = $res->json();
            $url  = $data['printAWBURL'] ?? null;
            if ($url) {
                OtoShipment::where('order_id', $orderId)->update(['label_url' => $url]);
            }
            return $url;
        } catch (\Throwable $e) {
            Log::warning('OTO tryFetchPrintLabel failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Helper: POST /orderStatus → returns the raw data array (or empty on failure)
     */
    protected function getOrderStatus(string $orderNumber): array
    {
        try {
            $res = $this->httpClient()->post($this->apiUrl . '/orderStatus', ['orderId' => $orderNumber]);
            return $res->json() ?? [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Fetch and save AWB label URL.
     * Tries GET /print/{orderNumber} first, then POST /orderStatus as fallback.
     */
    public function fetchLabel(string $otoId, int $orderId): array
    {
        try {
            $order       = Order::find($orderId);
            $orderNumber = $order?->order_number ?? $otoId;

            // 1. GET /print/{orderNumber}
            $response = $this->httpClient()->get($this->apiUrl . '/print/' . $orderNumber);
            $data     = $response->json();
            $labelUrl = $data['printAWBURL'] ?? $data['labelUrl'] ?? $data['label_url'] ?? null;

            if ($response->successful() && $labelUrl) {
                OtoShipment::where('order_id', $orderId)
                    ->update(['label_url' => $labelUrl, 'status' => 'confirmed']);
                return ['success' => true, 'label_url' => $labelUrl];
            }

            // 2. POST /orderStatus as fallback
            $statusData = $this->getOrderStatus($orderNumber);
            $labelUrl   = $statusData['printAWBURL'] ?? $statusData['labelUrl'] ?? null;

            if ($labelUrl) {
                OtoShipment::where('order_id', $orderId)
                    ->update(['label_url' => $labelUrl, 'status' => 'confirmed']);
                return ['success' => true, 'label_url' => $labelUrl];
            }

            // Check if OTO says shipment doesn't exist yet (OTO1113)
            $errorCode = $data['otoErrorCode'] ?? '';
            if ($errorCode === 'OTO1113') {
                return [
                    'success' => false,
                    'error'   => 'الشحنة غير موجودة في OTO بعد. يرجى إعادة إنشاء الشحنة.',
                    'no_shipment' => true,
                ];
            }

            $errorMsg = $data['otoErrorMessage'] ?? $data['errorMsg'] ?? $data['message'] ?? 'البوليصة غير جاهزة بعد';
            return ['success' => false, 'error' => $errorMsg];
        } catch (\Throwable $e) {
            Log::error('OTO fetchLabel exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Track a shipment
     */
    public function trackShipment(string $otoId): array
    {
        if (!$this->token) return ['success' => false, 'error' => 'OTO token missing'];

        try {
            $response = $this->httpClient()->get($this->apiUrl . '/trackShipment', ['otoId' => $otoId]);
            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                return ['success' => true, 'tracking' => $data['data'] ?? []];
            }

            return ['success' => false, 'error' => $data['message'] ?? 'Cannot track shipment', 'raw' => $data];
        } catch (\Throwable $e) {
            Log::error('OTO Track Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Cancel a shipment
     */
    public function cancelShipment(string $orderId, string $shipmentId): array
    {
        if (!$this->token) return ['success' => false, 'error' => 'OTO token missing'];

        try {
            $response = $this->httpClient()->post($this->apiUrl . '/cancelShipment', [
                'orderId'    => $orderId,
                'shipmentId' => $shipmentId,
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                $shipment = OtoShipment::where('oto_id', $shipmentId)->first();
                if ($shipment) $shipment->update(['status' => 'cancelled']);
                return ['success' => true];
            }

            return ['success' => false, 'error' => $data['message'] ?? 'Cannot cancel shipment', 'raw' => $data];
        } catch (\Throwable $e) {
            Log::error('OTO Cancel Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
