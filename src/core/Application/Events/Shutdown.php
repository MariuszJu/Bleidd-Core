<?php

namespace Bleidd\Application\Events;

use Bleidd\Event\AbstractEvent;

class Shutdown extends AbstractEvent
{
    
    /** @var string */
    public static $name = 'Application Shutdown';

    /** @var mixed */
    public $applicationTime;

    /**
     * Shutdown constructor
     *
     * @param mixed $applicationTime
     */
    public function __construct($applicationTime = null)
    {
        $this->applicationTime = $applicationTime;
    }
    
}
