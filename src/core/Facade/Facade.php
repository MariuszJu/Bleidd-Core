<?php

namespace Bleidd\Facade;

abstract class Facade
{

    /**
     * @return mixed
     */
    abstract static function getService();

    /**
     * @param string $name
     * @param array  $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments = [])
    {
        return is_callable($callback = [static::getService(), $name])
            ? forward_static_call_array($callback, $arguments)
            : null;
    }

}
