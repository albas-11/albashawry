<?php

return [
    // Mode: 'test' ou 'live'
    'mode' => env('TELR_MODE', 'test'),

    // URL de l'API
    'api_url' => 'https://secure.telr.com/gateway/order.json',

    // Identifiants
    'store_id' => env('TELR_STORE_ID'),
    'auth_key' => env('TELR_AUTH_KEY'),
    'api_url'    => env('TELR_API_URL'),
    'currency' => env('TELR_CURRENCY', 'SAR'),

    // URLs de retour (relatives à votre domaine)
    'return_url' => env('APP_URL') . '/payment/telr/callback',
    'cancel_url' => env('APP_URL') . '/payment/telr/cancel',
    'decline_url' => env('APP_URL') . '/payment/telr/decline',
];
