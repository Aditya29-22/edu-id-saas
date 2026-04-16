<?php

// config/services.php - Add these entries
return [
    // ... existing services

    'razorpay' => [
        'key' => env('RAZORPAY_KEY_ID'),
        'secret' => env('RAZORPAY_KEY_SECRET'),
    ],

    'cloudfront' => [
        'domain' => env('AWS_CLOUDFRONT_DOMAIN'),
        'distribution_id' => env('AWS_CLOUDFRONT_DISTRIBUTION_ID'),
    ],

    'aws' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'ap-south-1'),
    ],
];
