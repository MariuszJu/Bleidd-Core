<?php
/**
 * Created by PhpStorm.
 * User: inbs30
 * Date: 29.09.17
 * Time: 12:53
 */

namespace Bleidd\Router\Exception;

class RouterException extends \Exception
{

    /**
     * RouterException constructor
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
