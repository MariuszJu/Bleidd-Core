<?php

namespace Bleidd\Facade;

use Bleidd\Application\Runtime;

final class Cache
{

    const TTL_1M = 60;
    const TTL_5M = 300;
    const TTL_1H = 3600;
    const TTL_2H = 7200;
    const TTL_6H = 21600;
    const TTL_1D = 86400;
    const TTL_7D = 604800;
    const TTL_1MO = 2592000;

    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    /**
     * Get item with key
     *
     * @throws \Exception
     * @param string $key
     * @return mixed
     */
    public static function get(string $key)
    {
        return Runtime::cache()
            ->get($key);
    }

    /**
     * Set item in cache
     *
     * @throws \Exception
     * @param string   $key
     * @param string   $item
     * @param int|null $ttl
     * @return bool
     */
    public static function set(string $key, $item, int $ttl = null): bool
    {
        return Runtime::cache()
            ->set($key, $item, $ttl);
    }

    /**
     * Remove item from cache
     *
     * @throws \Exception
     * @param string $key
     * @return bool
     */
    public static function remove(string $key): bool
    {
        return Runtime::cache()
            ->remove($key);
    }

    /**
     * Check whether cache has item with given key
     *
     * @throws \Exception
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return Runtime::cache()
            ->has($key);
    }

}
