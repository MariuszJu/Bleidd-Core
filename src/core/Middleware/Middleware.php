<?php

namespace Bleidd\Middleware;

use Bleidd\Router\Route;
use Bleidd\Controller\Plugin\Response;
use Bleidd\Controller\AbstractController;

interface Middleware
{

    /**
     * @param AbstractController $controller
     * @param Route              $route
     * @param Response|null      $response
     * @return Response|null
     */
    public function __invoke(AbstractController $controller, Route $route, Response $response = null);

}