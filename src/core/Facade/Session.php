<?php

namespace Bleidd\Facade;

use Bleidd\Application\Runtime;

final class Session
{

    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    /**
     * @param string   $key
     * @param mixed    $value
     * @param int|null $ttl
     * @return \Bleidd\Session\Session
     */
    public static function set(string $key, $value, int $ttl = null): \Bleidd\Session\Session
    {
        return Runtime::session()
            ->set($key, $value, $ttl);
    }

    /**
     * @param string $key
     * @param null   $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return Runtime::session()
            ->get($key, $default);
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return Runtime::session()
            ->has($key);
    }

    /**
     * @param string $key
     * @return \Bleidd\Session\Session
     */
    public static function unset(string $key): \Bleidd\Session\Session
    {
        return Runtime::session()
            ->unset($key);
    }

    /**
     * @return \Bleidd\Session\Session
     */
    public static function clear(): \Bleidd\Session\Session
    {
        return Runtime::session()
            ->clear();
    }

}
