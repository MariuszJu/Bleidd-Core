<?php

namespace Bleidd\App\Admin;

use Bleidd\Application\Events\Shutdown;
use Bleidd\App\Admin\Listener\OnShutdown;
use Bleidd\Module\AbstractModuleProvider;
use Bleidd\App\Admin\Command\SampleCommand;

class Module extends AbstractModuleProvider
{

    /** @var string */
    public $name = 'Admin';
    
    public function boot()
    {
        $this->listen();
        $this->commands();
    }

    private function commands()
    {
        $this->registerCommand(SampleCommand::class);
    }
    
    private function listen()
    {
        $this->dispatcher()
            ->listen(Shutdown::class, OnShutdown::class);
    }

}
