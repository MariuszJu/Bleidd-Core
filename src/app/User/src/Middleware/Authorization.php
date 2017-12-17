<?php

namespace Bleidd\App\User\Middleware;

use Bleidd\Facade\Auth;
use Bleidd\Router\Route;
use Bleidd\Middleware\Middleware;
use Bleidd\Controller\Plugin\Response;
use Bleidd\Controller\AbstractController;

class Authorization implements Middleware
{

    /**
     * @param AbstractController $controller
     * @param Route              $route
     * @param Response|null      $response
     * @return Response|null
     */
    public function __invoke(AbstractController $controller, Route $route, Response $response = null)
    {
        if (!Auth::isAuthorized(\Bleidd\Authorization\Authorization::AREA_SYSTEM)) {
            return $controller
                ->redirect()
                ->toRoute('admin.login');
        }
    }

}
