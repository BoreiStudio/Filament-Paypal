<?php

return [
    'mode' => env('PAYPAL_MODE', 'single_tenant'),

    'tenant_model' => null,

    'webhooks' => [
        'route_prefix' => 'paypal/webhooks',
        'verify_signature' => true,
    ],

    'sandbox' => [
        'enabled' => env('PAYPAL_SANDBOX', true),
    ],
];
