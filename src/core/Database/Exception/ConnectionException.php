<?php

namespace Bleidd\Database\Exception;

class ConnectionException extends \Exception
{

    /**
     * ConnectionException constructor
     *
     * @param string          $message
     * @param int|null        $code
     * @param \Exception|null $previous
     */
    public function __construct(string $message, int $code = null, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
