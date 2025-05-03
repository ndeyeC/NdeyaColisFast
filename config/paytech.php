<?php

return [
    'api_key' => env('PAYTECH_API_KEY', ''),
    'api_secret' => env('PAYTECH_API_SECRET', ''),
    'base_url' => env('PAYTECH_BASE_URL', 'https://paytech.sn/api'),
    'currency' => env('PAYTECH_CURRENCY', 'XOF'),
    'env' => env('PAYTECH_ENV', 'test'),
    'ipn_url' => env('PAYTECH_IPN_URL', 'https://default-url.com/api/payment/ipn'), // URL par défaut si non définie

];