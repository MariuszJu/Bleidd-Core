<?php

namespace Bleidd\App\User;

use Bleidd\Module\AbstractModuleProvider;

class Module extends AbstractModuleProvider
{

    /** @var string */
    public $name = 'User';
    
    public function boot()
    {
        $this->listen();
    }
    
    private function listen()
    {

    }

}
