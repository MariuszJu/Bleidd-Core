<?php

namespace Bleidd\Facade;

use Bleidd\Application\Runtime;

final class URL
{

    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    /**
     * @return string
     */
    public static function uri(): string
    {
        return Runtime::request()
            ->uri();
    }

    /**
     * @param string $route
     * @param array  $params
     * @return string
     */
    public static function fromRoute(string $route, array $params = []): string
    {
        return Runtime::request()
            ->router()
            ->buildUrlFromRoute($route, $params);
    }

}
