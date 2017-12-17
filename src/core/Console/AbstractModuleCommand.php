<?php

namespace Bleidd\Console;

use Bleidd\Application\Runtime;

abstract class AbstractModuleCommand
{

    /** @var string */
    public $name;

    /** @var string */
    public $signature;

    /**
     * @param string|null $param
     * @return mixed
     */
    public function params(string $param = null)
    {
        return Runtime::console()->params($param);
    }

    public abstract function fire();

}
