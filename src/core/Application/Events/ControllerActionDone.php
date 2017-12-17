<?php

namespace Bleidd\Application\Events;

use Bleidd\Event\AbstractEvent;

class ControllerActionDone extends AbstractEvent
{
    
    /** @var string */
    public static $name = 'Controller Action Done';
    
    /** @var string */
    public $controllerClass;
    
    /** @var string */
    public $action;
    
    /** @var mixed */
    public $result;
    
    /**
     * ControllerActionDone constructor
     *
     * @param string $controllerClass
     * @param string $action
     * @param mixed  $result
     */
    public function __construct(string $controllerClass, string $action, &$result)
    {
        $this->controllerClass = $controllerClass;
        $this->action = $action;
        $this->result = $result;
    }
    
}
