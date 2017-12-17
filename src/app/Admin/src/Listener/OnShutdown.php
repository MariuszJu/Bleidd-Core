<?php

namespace Bleidd\App\Admin\Listener;

use Bleidd\Application\Events\Shutdown;

class OnShutdown
{

    /**
     * @param Shutdown $event
     */
    public function handle(Shutdown $event)
    {
        //echo 'Application shutdown after ' . $event->applicationTime;;
    }
    
}
