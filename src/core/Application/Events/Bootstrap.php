<?php

namespace Bleidd\Application\Events;

use Bleidd\Event\AbstractEvent;

class Bootstrap extends AbstractEvent
{
    
    /** @var string */
    public static $name = 'Application Bootstrap';
    
    /**
     * Bootstrap constructor
     */
    public function __construct()
    {
    
    }
    
}
