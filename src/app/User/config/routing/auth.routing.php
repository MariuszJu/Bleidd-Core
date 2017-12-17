<?php

use Bleidd\App\User\Controller\AuthController;

return [
    'login' => [
        'route'      => '/login',
        'controller' => AuthController::class,
    ],
    'logout' => [
        'route'      => '/logout',
        'controller' => AuthController::class,
    ],
];