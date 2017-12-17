<?php

namespace Bleidd\Application;

use Bleidd\Router\Route;
use Bleidd\Router\Router;
use Bleidd\Console\Writer;
use Bleidd\View\ViewModel;
use Bleidd\View\ViewRenderer;
use Bleidd\Middleware\Middleware;
use Bleidd\Module\ModulesManagment;
use Bleidd\Controller\Plugin\Response;
use Bleidd\Application\Events\Shutdown;
use Bleidd\Application\Events\Bootstrap;
use Bleidd\Controller\AbstractController;
use Bleidd\Application\Events\RouteMatched;
use Bleidd\Application\Events\ControllerActionDone;

final class Application
{

    /** @var bool */
    private static $initiated = false;

    /** @var $this */
    private static $self;

    private function __clone() {}
    private function __wakeup() {}

    /**
     * Application constructor
     */
    private function __construct()
    {
        self::$initiated = true;
    }

    /**
     * Initiate the whole App
     *
     * @param array $inputArgs
     * @throws \Exception
     */
    public static function init(array $inputArgs = [])
    {
        if (self::$initiated) {
            throw new \Exception('Application is already initiated!');
        }

        self::$self = new self();

        //ErrorHandler::registerErrorHandler();
        //ErrorHandler::registerExceptionHandler();

        App::setApplication(self::$self);
        Timer::logTime(Timer::BOOT);
        App::make(ModulesManagment::class)->loadModules();

        Runtime::dispatcher()->fire(new Bootstrap());

        if (Runtime::isCommandLineInterface()) {
            self::initCommandLineInterface($inputArgs);
            return;
        }
        
        /** @var $router Router */
        $router = App::make(Router::class);
        $route = $router->getMatchedRoute();

        Runtime::dispatcher()->fire(new RouteMatched($route));
        
        $controllerClass = $route->getControllerClass();
        
        if (!class_exists($controllerClass)) {
            throw new \Exception(sprintf('Called controller class %s does not exist', $controllerClass));
        }
        
        /** @var $controller AbstractController */
        $controller = App::make($controllerClass);
        $action = $route->getAction();

        Timer::logTime(Timer::BEFORE_MIDDLEWARES);
        self::executeMiddlewares($controller, $route);

        self::executeControllerAction($controller, $action);
    }

    /**
     * @param array $inputArgs
     */
    private static function initCommandLineInterface(array $inputArgs = [])
    {
        empty($args = count($inputArgs) > 1 ? array_slice($inputArgs, 1) : [])
            ? Runtime::console()->printCommands()
            : Runtime::console()->runCommand($args[0], array_slice($args, 1));

        self::shutdown();
    }

    /**
     * @param AbstractController $controller
     * @param string             $action
     * @throws \Exception
     */
    private static function executeControllerAction(AbstractController $controller, string $action)
    {
        $controllerClass = get_class($controller);

        if (!is_callable($callback = [$controller, $action . 'Action'])) {
            throw new \Exception(sprintf('Action %s not found in controller %s', $action, $controllerClass));
        }

        Timer::logTime(Timer::BEFORE_CONTROLLER);
        $controllerActionResult = call_user_func($callback);

        Runtime::dispatcher()->fire(new ControllerActionDone($controllerClass, $action, $controllerActionResult));
        Timer::logTime(Timer::AFTER_CONTROLLER);

        self::handleResponse($controllerActionResult, $controllerClass, $action);
        self::shutdown();
    }

    /**
     * @param AbstractController $controller
     * @param Route              $route
     */
    private static function executeMiddlewares(AbstractController $controller, Route $route)
    {
        $shutdown = false;
        $middlewares = array_merge($route->getMiddlewares(), Runtime::config()->configKey('global_middlewares', []));
        foreach ($middlewares as $middlewareClass) {
            $middleware = new $middlewareClass;

            if ($middleware instanceof Middleware
                && ($response = $middleware($controller, $route, $response ?? null)) instanceof Response
            ) {
                $shutdown = true;
                self::handleResponse($response);
            }
        }

        if ($shutdown) {
            self::shutdown();
            return;
        }
    }

    /**
     * @param mixed       $response
     * @param string|null $controllerClass
     * @param string|null $action
     */
    private static function handleResponse($response, string $controllerClass = null, string $action = null)
    {
        if ($response instanceof Response) {
            $response->prepareResponse(self::$self);
        } else if ($response instanceof ViewModel) {
            App::make(ViewRenderer::class)
                ->setViewModel($response
                    ->setControllerClass($controllerClass)
                    ->setActionName($action)
                )
                ->render();
        } else if (is_array($response) || empty($response)) {
            $view = (new ViewModel())
                ->setVariables(is_array($response) ? $response : [])
                ->setControllerClass($controllerClass)
                ->setActionName($action);

            App::make(ViewRenderer::class)
                ->setViewModel($view)
                ->render();
        }
    }

    /**
     * @return void
     */
    private static function shutdown()
    {
        Timer::logTime(Timer::SHUTDOWN);
        Runtime::dispatcher()->fire(new Shutdown(Timer::timeFromStart()));
    }

}
