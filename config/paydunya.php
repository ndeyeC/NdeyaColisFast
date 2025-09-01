<?php

return [
    'master_key'  => env('PAYDUNYA_MASTER_KEY', ''),
    'private_key' => env('PAYDUNYA_PRIVATE_KEY', ''),
    'public_key'  => env('PAYDUNYA_PUBLIC_KEY', ''),
    'token'       => env('PAYDUNYA_TOKEN', ''),
    
    // Mode : 'test' ou 'live'
    'mode'        => env('PAYDUNYA_MODE', 'test'),

    // Devise
    'currency'    => env('PAYDUNYA_CURRENCY', 'XOF'),

'callback_url'=> env('PAYDUNYA_CALLBACK_URL', '/tokens/payment/ipn'),
'return_url'  => env('PAYDUNYA_RETURN_URL', '/tokens/payment/success'),
'cancel_url'  => env('PAYDUNYA_CANCEL_URL', '/tokens/payment/cancel'),

];
