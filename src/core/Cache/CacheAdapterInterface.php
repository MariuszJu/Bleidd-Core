<?php

namespace Bleidd\Cache;

interface CacheAdapterInterface
{

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * @param string $key
     * @param        $item
     * @param int    $ttl
     * @return bool
     */
    public function set(string $key, $item, int $ttl): bool;

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @param string $key
     * @return bool
     */
    public function remove(string $key): bool;
    
}
