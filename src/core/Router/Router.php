<?php

namespace Bleidd\Router;

use Bleidd\Util\ArrayUtil;
use Bleidd\Util\Inflector;
use Bleidd\Application\Runtime;
use Bleidd\Router\Exception\RouterException;

class Router
{
    
    /** @var array */
    private $routes = [];
    
    /** @var Route */
    private $route;
    
    /**
     * Router constructor
     */
    public function __construct()
    {
        if ($modulesPath = Runtime::config()->configKey('modules_path')) {
            $modules = scandir($modulesPath);
            
            foreach ($modules as $module) {
                if (in_array($module, ['.', '..'])) {
                    continue;
                }
                
                $configFile = sprintf('%s/%s/config/routing.php', $modulesPath, $module);
                
                if (file_exists($configFile)) {
                    $routes = $this->resursiveParseChildRoutes(array_merge_recursive($this->routes, require $configFile));
                    $this->routes = $routes;
                }
            }
        }
    }

    /**
     * @throws \Exception
     * @param string $route
     * @param array  $params
     * @return string
     */
    public function buildUrlFromRoute(string $route, array $params = []): string
    {
        foreach ($this->routes as $routeName => $routeConfig) {
            if ($route == $routeName) {
                $url = $routeConfig['route'];
                $routeParams = $this->getRouteParams($url);
                
                foreach ($routeParams as $param) {
                    if (!isset($params[$param])) {
                        throw new \Exception(sprintf('Route param %s is required', $param));
                    }

                    $url = str_replace(':' . $param, $params[$param], $url);
                }

                return $url;
            }
        }

        throw new \Exception(sprintf('Could not find route with name %s', $route));
    }
    
    /**
     * @throws RouterException
     * @return array|bool
     */
    public function matchRoute()
    {
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING);
        $matchedRoute = false;
        
        if (strpos($uri, '?') !== false) {
            $uri = strtok($uri, '?');
        }
        
        if (empty($uri)) {
            $uri = '/';
        }
        
        if (!is_array($this->routes)) {
            throw new RouterException('There are no defined routes.');
        }

        $currentMethod = Runtime::request()->method();
        $uriParts = $this->getRouteParts($uri);
        
        foreach ($this->routes as $routeName => $route) {
            $matchedParams = [];
            $allowedMethods = $route['methods'] ?? [$currentMethod];

            if (!is_array($allowedMethods)) {
                $allowedMethods = [$allowedMethods];
            }
            foreach ($allowedMethods as &$method) {
                $method = strtoupper($method);
            }

            if (!in_array($currentMethod, $allowedMethods)) {
                continue;
            }

            if (!isset($route['route'])) {
                throw new RouterException(sprintf('%s route is invalid.', $routeName));
            }
            
            if ($uri == $route['route']) {
                $matchedRoute = [$routeName => $route];
                break;
            }

            $routeParts = $this->getRouteParts($route['route']);
            
            if (count($uriParts) != count($routeParts)) {
                continue;
            }

            $matchedRoute = [$routeName => $route];;
            foreach ($uriParts as $key => $uriPart) {
                if (empty($uriPart)) {
                    continue;
                }

                $routeParam = $routeParts[$key];
                if (!$this->matchPart($uriPart, $routeParam, $route)) {
                    $matchedRoute = false;
                    break;
                }

                if (strpos($routeParam, ':') !== false) {
                    $matchedParams[str_replace(':', '', $routeParam)] = $uriPart;
                }
            }

            if ($matchedRoute) {
                break;
            }
        }
        
        if ($matchedRoute) {
            $routeName = key($matchedRoute);
            $routeParams = reset($matchedRoute);

            if (strpos($routeName, '.') !== false) {
                $routeParts = explode('.', $routeName);
                $routeName = end($routeParts);
            }

            $action = $routeParams['action'] ?? Inflector::toCamelCase($routeName);
            $middlewares = ArrayUtil::wrapArray($routeParams['middlewares'] ?? []);
            
            $this->route = new Route(
                $routeName, $routeParams['route'], $routeParams['controller'], $action, $matchedParams, $middlewares
            );
        }
        
        if (!$this->route instanceof Route) {
            throw new RouterException(sprintf('There is no matched route for requested uri: %s', $uri));
        }
    }
    
    /**
     * @return Route
     */
    public function getMatchedRoute(): Route
    {
        if (!$this->route instanceof Route) {
            $this->matchRoute();
        }
        
        return $this->route;
    }

    /**
     * @param array $routes
     * @return array
     */
    private function resursiveParseChildRoutes(array $routes)
    {
        foreach ($routes as $route => $routeParams) {
            if (isset($routeParams['child_routes'])) {
                $childRouteParams = $this->resursiveParseChildRoutes($routeParams['child_routes']);

                foreach ($childRouteParams as $childRoute => $childRouteParams) {
                    $childRouteParams['route'] = sprintf('%s%s', $routeParams['route'], $childRouteParams['route']);
                    $routes[sprintf('%s.%s', $route, $childRoute)] = $childRouteParams;
                }

                unset($routes[$route]['child_routes']);
            }
        }

        return $routes;
    }
    
    /**
     * @param string $uriPart
     * @param string $routePart
     * @param array  $route
     * @return bool|
     */
    private function matchPart(string $uriPart,  string $routePart, array $route): bool
    {
        if ($uriPart === $routePart) {
            return true;
        }
        
        if (strpos($routePart, ':') === false) {
            return false;
        }
        
        $paramName = str_replace(':', '', $routePart);
        $paramPattern = isset($route['params'][$paramName]) ? $route['params'][$paramName] : '(.*)';
        
        $matched = preg_match(sprintf('/^%s$/', $paramPattern), $uriPart, $matches);

        if ($matched) {
            $this->matchedRouteParams[$paramName] = $uriPart;
        }
        
        return (bool) $matched;
    }
    
    /**
     * @param string $route
     * @return array
     */
    private function getRouteParts(string $route): array
    {
        $exploded = explode('/', $route);
        
        $parts = [];
        foreach ($exploded as $value) {
            if (!empty($value)) {
                $parts[] = $value;
            }
        }
        
        return $parts;
    }

    /**
     * @param string $route
     * @return array
     */
    private function getRouteParams(string $route): array
    {
        $params = [];

        foreach ($this->getRouteParts($route) as $part) {
            if (strpos($part, ':') !== false) {
                $params[] = str_replace(':', '', $part);
            }
        }

        return $params;
    }
    
}
