<?php

namespace Bleidd\Controller\Plugin;

use Bleidd\Controller\AbstractController;

class Redirect
{

    /** @var AbstractController */
    protected $controller;

    /**
     * Redirect constructor
     *
     * @param AbstractController $controller
     */
    public function __construct(AbstractController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * @param string $uri
     * @return Response
     */
    public function toUri(string $uri): Response
    {
        return $this->controller
            ->response()
            ->code(302)
            ->setHeader('Location', $uri);
    }

    /**
     * @param string $route
     * @param array  $params
     * @return Response
     */
    public function toRoute(string $route, array $params = []): Response
    {
        return $this->toUri($this->controller
            ->request()
            ->router()
            ->buildUrlFromRoute($route, $params)
        );
    }

    /**
     * @return Response
     */
    public function refresh(): Response
    {
        return $this->toUri($this->controller
            ->request()
            ->uri()
        );
    }

}
