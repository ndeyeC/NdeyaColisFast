<?php

// config/cinetpay.php
return [
    'api_key' => env('CINETPAY_API_KEY', ''),
    'site_id' => env('CINETPAY_SITE_ID', ''),
    'secret_key' => env('CINETPAY_SECRET_KEY', ''),
    'base_url' => env('CINETPAY_BASE_URL', 'https://api-checkout.cinetpay.com'),
    'currency' => env('CINETPAY_CURRENCY', 'XOF'),
    'env' => env('CINETPAY_ENV', 'test'), // test ou prod
    'version' => 'v2',

    // 'url_success' => env('CINETPAY_URL_SUCCESS'),
    // 'url_ipn'     => env('CINETPAY_URL_IPN'),
    // 'url_cancel'  => env('CINETPAY_URL_CANCEL'),
];