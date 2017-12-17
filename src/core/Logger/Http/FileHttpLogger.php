<?php

namespace Bleidd\Logger\Http;

use Bleidd\Util\FileReader;
use Bleidd\Application\Runtime;

class FileHttpLogger implements HttpLogger
{

    /** @var FileReader */
    protected $reader;

    /**
     * FileHttpLogger constructor
     *
     * @param FileReader $reader
     */
    public function __construct(FileReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Init logger
     */
    protected function init()
    {
        $logFile = Runtime::config()->configKey('loggers.http.logs_file');
        $this->reader->init($logFile, FileReader::MODE_READ_WRITE_APPEND_CREATE, true);
    }

    /**
     * Log HTTP request into a file
     */
    public function logRequest()
    {
        $this->init();
        $request = Runtime::request();
        $method = $request->method();

        $this->reader->writeLine(sprintf('%s | %s | %s %s %s %s %s',
            (new \DateTime())->format('Y-m-d H:i:s'),
            $request->httpCode(), $request->protocol(), $method, $request->uri(),
            in_array($method, ['POST', 'PUT', 'PATCH']) ? json_encode($request->input()) : '',
            $request->requestTime())
        );
        $this->reader->closeFile();
    }

    /**
     * @return array
     */
    public function getRequests(): array
    {

    }

}
