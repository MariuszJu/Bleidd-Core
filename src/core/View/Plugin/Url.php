<?php

namespace Bleidd\View\Plugin;

use Bleidd\Application\Runtime;

class Url
{

    /**
     * @param array $parameters
     * @return mixed
     */
    public function __invoke(array $parameters = [])
    {
        try {
            return Runtime::request()
                ->router()
                ->buildUrlFromRoute($parameters[0], $parameters[1] ?? []);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}
