<?php

return [
    'tests' => [
        // Class definitions
        'classes' => [
            'state' => 'Laraquick\Tests\State'
        ],
        'commands' => [
            // commands to run in the setUp method
            'set_up' => [
                // These would only run once in the test life cycle
                'once' => [
                    'migrate:fresh' => [
                        // options
                        // '--path' => 'database/migrations'
                    ]
                ],
                // These would always run before each test
                'always' => [

                ]
            ],
            // These would always run after each test
            'tear_down' => [

            ]
        ],
        // Headers to pass into every request
        'headers' => [],
        // Use jwt token based on the tymon/jwt-auth package
        'jwt' => false,
        // The information of the user to create when a user is required. Also used when logging in.
        // The instance is picked from config auth.providers.users.model
        'user_info' => [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'jdoe@email.com'
        ],
    ],
    'websocket' => [
        // 0.0.0.0 <=> any ip address
        'allowed_ip_address' => '0.0.0.0',
        // The websocket controller
        'controller' => 'Laraquick\\Controllers\\WebSocketController',
        // port to run websocket on
        'port' => '8080'
    ]
];
