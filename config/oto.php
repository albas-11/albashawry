<?php

return [
    //OTO API Configuration
    'refresh_token' => env('OTO_REFRESH_TOKEN'),
    'api_url' => env('OTO_API_URL'),
    'store_name' => env('OTO_STORE_NAME', 'AL Bashawri'),

    //Authentication
    'token_cache_key' => 'oto_bearer_token',
    'token_expiry' => 58, // minutes (OTO tokens expire after 60 min)

    //Default Pickup Location
    'default_pickup' => [
        'code' => env('OTO_WAREHOUSE_CODE', 'WH-01'),
        'name' => env('OTO_WAREHOUSE_NAME', 'Main Warehouse'),
        'mobile' => env('OTO_WAREHOUSE_PHONE'),
        'address' => env('OTO_WAREHOUSE_ADDRESS'),
        'city' => env('OTO_WAREHOUSE_CITY', 'Casablanca'),
        'country' => env('OTO_WAREHOUSE_COUNTRY', 'MA'),
    ],

    //Webhook
    'webhook_secret' => env('OTO_WEBHOOK_SECRET'),
    'webhook_url' => env('APP_URL') . '/webhooks/oto',
];
