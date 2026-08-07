<?php

return [
    'key_id' => env('RAZORPAY_KEY_ID'),
    'key_secret_id' => env('RAZORPAY_KEY_SECRET_ID'),
    'webhook' => [
        'secret' => env('RAZORPAY_WEBHOOK_SECRET'),
    ]
];
