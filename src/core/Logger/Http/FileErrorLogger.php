<?php

namespace Bleidd\Logger\Http;

use Bleidd\Util\FileReader;
use Bleidd\Application\Runtime;

class FileErrorLogger implements ErrorLogger
{

    /** @var FileReader */
    protected $reader;

    /** @var string */
    protected $separator = '-----------';

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
        $logFile = Runtime::config()->configKey('loggers.errors.logs_file');
        $this->reader->init($logFile, FileReader::MODE_READ_WRITE_APPEND_CREATE, true);
    }

    /**
     * @param string $type
     * @param string $error
     * @param string $file
     * @param int    $line
     */
    public function logError(string $type, string $error, string $file, int $line)
    {
        $this->init();

        $this->reader
            ->writeLine(sprintf('%s | %s | %s in %s at line %s',
                (new \DateTime())->format('Y-m-d H:i:s'), $type, $error, $file, $line
            ))
            ->writeLine($this->separator)
            ->closeFile();
    }

    /**
     * @param string $message
     * @param string $file
     * @param int    $line
     * @param string $stackTrace
     */
    public function logException(string $message, string $file, int $line, string $stackTrace)
    {
        $this->init();

        $this->reader
            ->writeLine(sprintf('%s | EXCEPTION | %s in %s at line %s%s%s',
                (new \DateTime())->format('Y-m-d H:i:s'), $message, $file, $line, PHP_EOL, $stackTrace
            ))
            ->writeLine($this->separator)
            ->closeFile();
    }

}
