<?php

namespace Bleidd\Cache\Adapter;

use Bleidd\Cache\CacheAdapterInterface;

class DbCacheAdapter implements CacheAdapterInterface
{

    /**
     * @param string $key
     */
    public function get(string $key)
    {
        // TODO: Implement get() method.
    }

    /**
     * @param string $key
     * @param mixed  $item
     * @param int    $ttl
     * @return bool
     */
    public function set(string $key, $item, int $ttl): bool
    {
        // TODO: Implement set() method.
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        // TODO: Implement has() method.
    }

    /**
     * @param string $key
     * @return bool
     */
    public function remove(string $key): bool
    {
        // TODO: Implement remove() method.
    }

}
