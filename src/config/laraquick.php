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
];
