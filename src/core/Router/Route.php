<?php

namespace Bleidd\Router;

class Route
{
    
    /** @var string */
    protected $name;
    
    /** @var string */
    protected $route;
    
    /** @var string */
    protected $controllerClass;
    
    /** @var string */
    protected $action;
    
    /** @var array */
    protected $params;

    /** @var array */
    protected $middlewares;
    
    /**
     * Route constructor
     *
     * @param string $name
     * @param string $route
     * @param string $controllerClass
     * @param string $action
     * @param array  $params
     * @param array  $middlewares
     */
    public function __construct(string $name, string $route, string $controllerClass,
                                string $action, array $params = [], array $middlewares = [])
    {
        $this->name = $name;
        $this->route = $route;
        $this->controllerClass = $controllerClass;
        $this->action = $action;
        $this->params = $params;
        $this->middlewares = $middlewares;
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @return string
     */
    public function getControllerClass(): string
    {
        return $this->controllerClass;
    }
    
    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return is_array($this->params) ? $this->params : [];
    }

    /**
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

}
