<?php

return [
    'key_id' => env('RAZORPAY_KEY_ID'),
    'key_secret_id' => env('RAZORPAY_KEY_SECRET_ID'),
    'webhook' => [
        'signature' => env('RAZORPAY_WEBHOOK_SIGNATURE'),
        'secret' => env('RAZORPAY_WEBHOOK_SECRET'),
    ]
];
