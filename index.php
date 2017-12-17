<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('ROOT_DIR', __DIR__);

require_once 'autoloader.php';
require_once 'vendor/autoload.php';

\Bleidd\Application\Application::init($argv ?? []);

if (($logger = \Bleidd\Application\Runtime::config()->configKey('loggers.http')) && $logger['active']) {
    \Bleidd\Application\App::make($logger['class'])
        ->logRequest();
}