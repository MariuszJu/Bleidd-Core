<?php

use Bleidd\App\Admin\Controller\AdminController;

return [
    'admin' => [
        'route'       => '/admin',
        'controller'  => AdminController::class,
        'middlewares' => [
            \Bleidd\App\User\Middleware\Authorization::class,
        ],
    ],
];