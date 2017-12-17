<?php

namespace Bleidd\Application;

use Bleidd\Cache\Cache;
use Bleidd\Util\Config;
use Bleidd\Console\Console;
use Bleidd\Request\Request;
use Bleidd\Session\Session;
use Bleidd\Event\Dispatcher;
use Bleidd\Language\Language;
use Bleidd\Authorization\Authorization;
use Bleidd\View\QuickViewModel;
use Bleidd\View\ViewRenderer;

final class Runtime
{

    /**
     * @return Request
     */
    public static function request(): Request
    {
        return App::make(Request::class);
    }
    
    /**
     * @return Dispatcher
     */
    public static function dispatcher(): Dispatcher
    {
        return App::make(Dispatcher::class);
    }

    /**
     * @return Session
     */
    public static function session(): Session
    {
        return App::make(Session::class);
    }

    /**
     * @return Config
     */
    public static function config(): Config
    {
        return App::make(Config::class);
    }

    /**
     * @return Cache
     */
    public static function cache(): Cache
    {
        return App::make(Cache::class);
    }

    /**
     * @throws \Exception
     * @return Console
     */
    public static function console(): Console
    {
        if (!self::isCommandLineInterface()) {
            throw new \Exception('Could not instantiate Console class when not in CLI!');
        }

        return App::make(Console::class);
    }

    /**
     * @return Language
     */
    public static function language(): Language
    {
        return App::make(Language::class);
    }

    /**
     * @return Authorization
     */
    public static function auth(): Authorization
    {
        return App::make(Authorization::class);
    }

    /**
     * @param string $template
     * @param array  $variables
     * @return ViewRenderer
     */
    public static function view(string $template, array $variables = []): ViewRenderer
    {
        return (new ViewRenderer())
            ->setViewModel((new QuickViewModel($template))
                ->setVariables($variables)
            );
    }

    /**
     * @return bool
     */
    public static function isCommandLineInterface(): bool
    {
        return self::sapiName() === 'cli';
    }

    /**
     * @return string
     */
    public static function sapiName(): string
    {
        return strtolower(php_sapi_name());
    }

    /**
     * @return bool
     */
    public static function isWindows(): bool
    {
        return strpos(self::os(), 'win') !== false;
    }

    /**
     * @return bool
     */
    public static function isLinux(): bool
    {
        return strpos(self::os(), 'linux') !== false;
    }

    /**
     * @return string
     */
    public static function os(): string
    {
        return strtolower(PHP_OS);
    }

}
