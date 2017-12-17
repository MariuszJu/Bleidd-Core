<?php

return [
    'admin' => [
        'child_routes' => array_merge(
            include __DIR__ . '/routing/auth.routing.php'
        )
    ]
];