<?php

namespace Bleidd\Application;

use Bleidd\Request\Request;

final class ErrorHandler
{

    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    /**
     * Init handler
     */
    private static function init()
    {
        if (!defined('E_FATAL')) {
            define('E_FATAL', E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR);
        }
    }

    /**
     * Register error and exception handler
     */
    public static function registerErrorHandler()
    {
        self::init();

        set_error_handler(sprintf('%s::handler', __CLASS__));

        register_shutdown_function(function() {
            if (($error = error_get_last()) && ($error['type'] & E_FATAL)) {
                self::handler($error['type'], $error['message'], $error['file'], $error['line']);
            }
        });
    }

    /**
     * Register Exception handler
     */
    public static function registerExceptionHandler()
    {
        self::init();
        set_exception_handler(sprintf('%s::exceptionHandler', __CLASS__));        
    }

    /**
     * @param \Exception|\TypeError $exception
     */
    public static function exceptionHandler($exception)
    {
        if (($logger = Runtime::config()->configKey('loggers.errors')) && $logger['active']) {
            App::make($logger['class'])
                ->logException($exception->getMessage(), $exception->getFile(), $exception->getLine(), $exception->getTraceAsString());
        }

        Runtime::request()
            ->httpCode(Request::HTTP_CODE_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     */
    public static function handler(int $errno, string $errstr, string $errfile, int $errline)
    {
        switch ($errno) {
            case E_ERROR: // 1 //
                $typestr = 'ERROR';
                break;
            case E_WARNING: // 2 //
                $typestr = 'WARNING';
                break;
            case E_PARSE: // 4 //
                $typestr = 'PARSE';
                break;
            case E_NOTICE: // 8 //
                $typestr = 'NOTICE';
                break;
            case E_CORE_ERROR: // 16 //
                $typestr = 'CORE_ERROR';
                break;
            case E_CORE_WARNING: // 32 //
                $typestr = 'CORE_WARNING';
                break;
            case E_COMPILE_ERROR: // 64 //
                $typestr = 'COMPILE_ERROR';
                break;
            case E_CORE_WARNING: // 128 //
                $typestr = 'COMPILE_WARNING';
                break;
            case E_USER_ERROR: // 256 //
                $typestr = 'USER_ERROR';
                break;
            case E_USER_WARNING: // 512 //
                $typestr = 'USER_WARNING';
                break;
            case E_USER_NOTICE: // 1024 //
                $typestr = 'USER_NOTICE';
                break;
            case E_STRICT: // 2048 //
                $typestr = 'STRICT';
                break;
            case E_RECOVERABLE_ERROR: // 4096 //
                $typestr = 'ERROR';
                break;
            case E_DEPRECATED: // 8192 //
                $typestr = 'DEPRECATED';
                break;
            case E_USER_DEPRECATED: // 16384 //
                $typestr = 'USER_DEPRECATED';
                break;
        }

        if (($logger = Runtime::config()->configKey('loggers.errors')) && $logger['active']) {
            App::make($logger['class'])
                ->logError($typestr, $errstr, $errfile, $errline);
        }

        Runtime::request()
            ->httpCode(Request::HTTP_CODE_INTERNAL_SERVER_ERROR);
    }

}