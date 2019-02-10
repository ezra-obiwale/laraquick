<?php

return [
    'tests' => [
        'commands' => [
            // commands to run in the setUp method
            'set_up' => [
                // These would only run once in the test life cycle
                'once' => [
                    'migrate:fresh' => [
                        // options
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
        'user_array' => [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'jdoe@email.com'
        ],
        'classes' => [
            'auth_guard' => 'Tymon\JWTAuth\Facades\JWTAuth',
            'state' => 'Laraquick\Tests\State'
        ]
    ],
    'websocket' => [
        'allowed_ip_address' => '0.0.0.0', // 0.0.0.0 <=> any ip address
        'port' => '8080', // port to run websocket on
        'controller' => 'Laraquick\\Controllers\\WebSocketController'
    ]
];
