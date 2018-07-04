<?php

return [
    'websocket' => [
        'allowed_ip_address' => '0.0.0.0', // 0.0.0.0 <=> any ip address
        'port' => '8080', // port to run websocket on
        'controller' => 'Laraquick\\Controllers\\WebSocketController'
    ]
];
