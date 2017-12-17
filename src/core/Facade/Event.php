<?php

namespace Bleidd\Facade;

use Bleidd\Application\Runtime;

final class Event
{

    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    /**
     * @param string|object $event
     * @param array|null    $params
     * @return array
     */
    public static function fire($event, array $params = null)
    {
        return Runtime::dispatcher()
            ->fire($event, $params);
    }

    /**
     * @param string $event
     * @param mixed  $listener
     * @param int    $priority
     */
    public static function listen(string $event, $listener, int $priority = 0)
    {
        Runtime::dispatcher()
            ->listen($event, $listener, $priority);
    }

}
