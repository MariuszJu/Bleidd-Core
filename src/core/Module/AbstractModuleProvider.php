<?php

namespace Bleidd\Module;

use Bleidd\Application\App;
use Bleidd\Console\Console;
use Bleidd\Event\Dispatcher;
use Bleidd\Application\Runtime;
use Bleidd\Console\AbstractModuleCommand;

abstract class AbstractModuleProvider
{
    
    /** @var string */
    public $name;

    public abstract function boot();
    
    /**
     * @return Dispatcher
     */
    public function dispatcher(): Dispatcher
    {
        return Runtime::dispatcher();
    }

    /**
     * @throws \Exception
     * @param $command AbstractModuleCommand|string
     * @return Console|void
     */
    public function registerCommand($command)
    {
        if (!Runtime::isCommandLineInterface()) {
            return;
        }

        if ((is_string($command) && ($command = App::make($command)) instanceof AbstractModuleCommand)
            || $command instanceof AbstractModuleCommand
        ) {
            return Runtime::console()->registerCommand($command, $this->name);
        }

        throw new \Exception('Invalid command provided');
    }

}
