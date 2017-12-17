<?php

namespace Bleidd\Facade;

use Bleidd\Application\Runtime;

final class View
{

    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    /**
     * @param string $template
     * @param array  $variables
     */
    public function render(string $template, array $variables = [])
    {
        return Runtime::view($template, $variables)
            ->render();
    }

}
