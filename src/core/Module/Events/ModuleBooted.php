<?php

namespace Bleidd\Module\Events;

use Bleidd\Event\AbstractEvent;

class ModuleBooted extends AbstractEvent
{
    
    /** @var string */
    public static $name = 'Module Booted';
    
    /** @var string */
    public $namespace;

    /** @var string */
    public $moduleName;

    /** @var string */
    public $path;

    /** @var array */
    public $modules;

    /**
     * ModuleBooted constructor
     *
     * @param string $namespace
     * @param string $moduleName
     * @param string $path
     * @param array  $modules
     */
    public function __construct(string $namespace, string $moduleName, string $path, array $modules)
    {
        $this->namespace = $namespace;
        $this->moduleName = $moduleName;
        $this->path = $path;
        $this->modules = $modules;
    }
    
}
