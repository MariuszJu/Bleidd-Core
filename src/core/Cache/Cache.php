<?php

namespace Bleidd\Cache;

use Bleidd\Application\App;
use Bleidd\Application\Application;
use Bleidd\Cache\Adapter\FileCacheAdapter;

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

    /** @var CacheAdapterInterface */
    protected $adapter;

    /** @var bool */
    protected $throwExceptions = false;

    /** @var string */
    protected $defaultAdapter = FileCacheAdapter::class;

    private function __clone() {}
    private function __wakeup() {}

    /**
     * Cache constructor
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {

    }

    /**
     * Set whether throw exceptions
     *
     * @param bool $throwExceptions
     * @return $this
     */
    public function setThrowExceptions($throwExceptions): self
    {
        $this->throwExceptions = $throwExceptions;
        return $this;
    }

    /**
     * Set cache adapter
     *
     * @param CacheAdapterInterface $adapter
     * @return $this
     */
    public function setAdapter(CacheAdapterInterface $adapter): self
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * Make sure adapter is set
     */
    private function init()
    {
        if (!$this->adapter instanceof CacheAdapterInterface) {
            $this->adapter = App::make($this->defaultAdapter);
        }
    }

    /**
     * Get item with key
     *
     * @throws \Exception
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $this->init();

        try {
            return $this->adapter->get($key);
        } catch (\Exception $e) {
            if ($this->throwExceptions) {
                throw $e;
            }

            return null;
        }
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
    public function set(string $key, $item, int $ttl = null): bool
    {
        $this->init();

        if (empty($ttl)) {
            $ttl = self::TTL_1D;
        }

        try {
            return $this->adapter->set($key, $item, $ttl);
        } catch (\Exception $e) {
            if ($this->throwExceptions) {
                throw $e;
            }

            return false;
        }
    }

	/**
	 * Remove item from cache
	 *
	 * @throws \Exception
	 * @param string $key
	 * @return bool
	 */
    public function remove(string $key): bool
    {
	    $this->init();

	    try {
		    return $this->adapter->remove($key);
	    } catch (\Exception $e) {
		    if ($this->throwExceptions) {
			    throw $e;
		    }

		    return false;
	    }
    }

    /**
     * Check whether cache has item with given key
     *
     * @throws \Exception
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $this->init();

        try {
            return $this->adapter->has($key);
        } catch (\Exception $e) {
            if ($this->throwExceptions) {
                throw $e;
            }

            return false;
        }
    }

}
