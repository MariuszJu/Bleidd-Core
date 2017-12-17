<?php

namespace Bleidd\Util;

class FileReader
{
    
    const MODE_READ = 'r';
    const MODE_READ_WRITE = 'r+';
    const MODE_WRITE_CREATE = 'w';
    const MODE_READ_WRITE_CREATE = 'w+';
    const MODE_WRITE_APPEND_CREATE = 'a';
    const MODE_READ_WRITE_APPEND_CREATE = 'a+';

    /** @var resource|null */
    protected $file;

    /** @var string */
    protected $filePath;

    /**
     * @throws \Exception
     * @param string $filePath
     * @param string $mode
     * @param bool   $createDirectory
     * @return $this
     */
    public function init(string $filePath, string $mode = 'a+', bool $createDirectory = true): self
    {
        if ($createDirectory) {
            $dir = dirname($filePath);

            if (!is_dir($dir)) {
                mkdir($dir, 0777);
            }
        }

        $this->file = fopen($filePath, $mode);
        $this->filePath = $filePath;

        if (!$this->file) {
            throw new \Exception(sprintf('Could not open file %s', $filePath));
        }

        return $this;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    public function unlink(string $filePath): bool
    {
        return unlink($filePath);
    }

    /**
     * @param string $path
     * @return array
     */
    public function readLocation(string $path): array
    {
        $entries = [];
        foreach (scandir($path) as $entry) {
            if (in_array($entry, ['.', '..'])) {
                continue;
            }

            $entries[] = $entry;
        }

        return $entries;
    }

    /**
     * @throws \Exception
     * @param string $filePath
     * @return bool|string
     */
    public function fileContent(string $filePath)
    {
        if (!is_readable($filePath)) {
            throw new \Exception(sprintf('%s is not readable', $filePath));
        }

        return file_get_contents($filePath);
    }

    /**
     * @param string $line
     * @param bool   $withNewLine
     * @return self
     */
    public function writeLine(string $line, bool $withNewLine = true): self
    {
        fwrite($this->file, $line);
        $withNewLine && $this->newLine();

        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function write(string $content)
    {
        return $this->writeLine($content, false);
    }

    /**
     * @return string
     */
    public function content(): string
    {
        return fread($this->file, filesize($this->filePath));
    }

    /**
     * Write new line to file
     *
     * @return self
     */
    public function newLine(): self
    {
        fwrite($this->file, "\n");

        return $this;
    }

    /**
     * Close file
     *
     * @return self
     */
    public function closeFile(): self
    {
        fclose($this->file);
        $this->file = null;

        return $this;
    }
    
}
