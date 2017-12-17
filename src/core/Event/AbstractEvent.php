<?php

namespace Bleidd\Event;

use Bleidd\Util\Inflector;

abstract class AbstractEvent
{
    
    /** @var string */
    public static $name;
    
    /**
     * @return string
     */
    public function __toString(): string
    {
        return get_class($this);
    }
    
    /**
     * @return string
     */
    public function systemName(): string
    {
        return strtolower(Inflector::to_underscore(str_replace('\\', '', get_class($this))));
    }
    
}
