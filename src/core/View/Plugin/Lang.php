<?php

namespace Bleidd\View\Plugin;

use Bleidd\Application\Runtime;

class Lang
{

    /**
     * @param array $parameters
     * @return mixed
     */
    public function __invoke(array $parameters = [])
    {
        try {
            return Runtime::language()
                ->translate($parameters[0] ?? '', $parameters[1] ?? null);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}
