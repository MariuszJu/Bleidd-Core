<?php

namespace Bleidd\Facade;

use Bleidd\Application\Runtime;

final class Config
{

    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    /**
     * @param array ...$args
     * @return mixed
     */
    public static function configKey(...$args)
    {
        return Runtime::config()
            ->configKey(...$args);
    }

}
