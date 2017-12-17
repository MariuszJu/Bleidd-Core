<?php

namespace Bleidd\Logger\Http;

interface ErrorLogger
{

    /**
     * @param string $type
     * @param string $error
     * @param string $file
     * @param int    $line
     */
    public function logError(string $type, string $error, string $file, int $line);

    /**
     * @param string $message
     * @param string $file
     * @param int    $line
     * @param string $stackTrace
     */
    public function logException(string $message, string $file, int $line, string $stackTrace);

}
