<?php

return [
    'client_key'           => env('MIDTRANS_CLIENT_KEY', ''),
    'server_key'           => env('MIDTRANS_SERVER_KEY', ''),
    'is_production'        => env('MIDTRANS_IS_PRODUCTION', false),
    'snap_js_url'          => env('MIDTRANS_SNAP_JS_URL', 'https://app.sandbox.midtrans.com/snap/snap.js'),
    'order_prefix'         => env('MIDTRANS_ORDER_PREFIX', 'INV'),
    'riplabs_key'          => env('RIPLABS_KEY', ''),
    'riplabs_snaptoken_url'=> env('RIPLABS_SNAPTOKEN_URL', 'https://restapi.riplabs.co.id/snaptokenppdbyayasan/getsnaptoken'),
    'callback_key'         => env('MIDTRANS_CALLBACK_KEY', ''),
];
