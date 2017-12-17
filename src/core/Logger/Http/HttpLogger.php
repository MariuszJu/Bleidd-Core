<?php

namespace Bleidd\Logger\Http;

interface HttpLogger
{

    /**
     * @return mixed
     */
    public function logRequest();

    /**
     * @return array
     */
    public function getRequests(): array;

}
