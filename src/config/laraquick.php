<?php

return [
    'classes' => [
        // User model must use \Laraquick\Models\Traits\Notifiable
        'database_notification' => \Illuminate\Notifications\DatabaseNotification::class
    ],
    'controllers' => [
        'use_policies' => false
    ],
    'tests' => [
        // Headers to pass into every request
        'headers' => [],

        // Use jwt token based on the tymon/jwt-auth package
        'jwt' => false,

        'responses' => [

            // The path in the storage where responses are to be stored
            'storage_path' => 'test-responses',

            // The file format for all stored test responses
            'format' => 'json',

        ],
    ],
    'websocket' => [
        // 0.0.0.0 <=> any ip address
        'allowed_ip_address' => env('WEBSOCKET_ALLOWED_IP_ADDRESS', '0.0.0.0'),

        // The websocket controller
        'controller' => 'Laraquick\\Controllers\\WebSocketController',

        // port to run websocket on
        'port' => env('WEBSOCKET_PORT', 8080),

        // the channels to listen to
        'channels' => [
            'events'
        ],
    ]
];
