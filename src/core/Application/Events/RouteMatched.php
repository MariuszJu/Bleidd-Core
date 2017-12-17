<?php

namespace Bleidd\Application\Events;

use Bleidd\Router\Route;
use Bleidd\Event\AbstractEvent;

class RouteMatched extends AbstractEvent
{
    
    /** @var string */
    public static $name = 'Before Controller Action';
    
    /** @var Route */
    public $route;
    
    /**
     * RouteMatched constructor
     *
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }
    
}
