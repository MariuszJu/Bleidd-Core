<?php

namespace Bleidd\Console;

use Bleidd\Application\Runtime;
use Bleidd\Application\Application;

final class Console
{

    const COLOR_BLACK = '0;30';
    const COLOR_DARKGRAY = '1;30';
    const COLOR_BLUE = '0;34';
    const COLOR_GREEN = '0;32';
    const COLOR_LIGHTGREEN = '1;32';
    const COLOR_CYAN = '0;36';
    const COLOR_LIGHTCYAN = '1;36';
    const COLOR_RED = '0;31';
    const COLOR_LIGHTRED = '1;31';
    const COLOR_PURPLE = '0;35';
    const COLOR_BROWN = '0;33';
    const COLOR_YELLOW = '1;33';
    const COLOR_LIGHTGRAY = '0;37';
    const COLOR_WHITE = '1;37';

    const COLOR_BACKGROUND_BLACK = 40;
    const COLOR_BACKGROUND_RED = 41;
    const COLOR_BACKGROUND_GREEN = 42;
    const COLOR_BACKGROUND_YELLOW = 43;
    const COLOR_BACKGROUND_BLUE = 44;
    const COLOR_BACKGROUND_MAGNETA = 45;
    const COLOR_BACKGROUND_CYAN = 46;
    const COLOR_BACKGROUND_LIGHTGRAY = 47;

    /** @var AbstractModuleCommand[] */
    private $commands;

    /** @var array */
    private $params = [];

    private function __clone() {}
    private function __wakeup() {}

    /**
     * Session constructor
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {

    }

    /**
     * @param AbstractModuleCommand $command
     * @param string                $category
     * @return $this
     */
    public function registerCommand(AbstractModuleCommand $command, string $category = null): self
    {
        $this->commands[$category ?? 'default'][] = $command;
        return $this;
    }

    /**
     * @return void
     */
    public function printCommands()
    {
        $this->writeInfo('----------------------------------');
        $this->writeInfo('----- Available CLI commands -----');
        $this->writeInfo('----------------------------------');
        $this->newLine();

        if (empty($this->commands)) {
            $this->writeWarning('No commands registered');
        }

        foreach ($this->commands as $category => $commands) {
            $this->write($category, true, self::COLOR_LIGHTGREEN);

            /** @var $commands AbstractModuleCommand[] */
            foreach ($commands as $command) {
                $this->write(' ' . $command->name, true, self::COLOR_CYAN);
            }
        }

        $this->newLine();
    }

    /**
     * @param string|null $param
     * @return mixed
     */
    public function params(string $param = null)
    {
        if (empty($param)) {
            return $this->params;
        }

        return $this->params[$param] ?? null;
    }

    /**
     * @param string $commandName
     * @param array  $args
     */
    public function runCommand(string $commandName, array $args = [])
    {
        try {
            $command = $this->searchCommand($commandName);
        } catch (\Exception $e) {
            $this->writeError($e->getMessage());
            return;
        }

        $this->params = $this->parseParams(
            !empty($command->signature) ? $command->signature : $command->name, $commandName,$args
        );
        $command->fire($args);
    }

    /**
     * @param string $text
     * @param bool   $newLine
     * @param mixed  $color
     * @param mixed  $backgroundColor
     * @return $this
     */
    public function write(string $text, bool $newLine = true, $color = null, $backgroundColor = null): self
    {
        $string = Runtime::isWindows() ? $text : $this->getColoredText($text, $color);
        echo $string;
        $newLine && $this->newLine();

        return $this;
    }

    /**
     * @return Console
     */
    public function newLine()
    {
        return $this->write(PHP_EOL, false);
    }

    /**
     * @param string       $text
     * @param string|array $description
     * @return $this
     */
    public function writeHeading(string $text, string $description = null)
    {
        $separator = '';
        $separatorChar = '-';
        $separatorCharsCount = 20;

        if (($length = strlen($text)) > $separatorCharsCount) {
            $separatorCharsCount = $length + 5;
        }

        for ($i = 0 ; $i < $separatorCharsCount ; $i++) {
            $separator .= $separatorChar;
        }

        $this->writeInfo(sprintf('|%s', $separator));
        $this->writeInfo(sprintf('| %s', $text));
        $this->writeInfo(sprintf('|%s', $separator));

        if (!empty($description)) {
            $this->writeInfo('|');
            $this->writeInfo(sprintf('| %s', $description));
            $this->writeInfo('|');
        }

        return $this;
    }

    /**
     * @param string $text
     * @param string $separatorChar
     * @param int    $separatorCharsCount
     * @return $this
     */
    public function writeCustomHeading(string $text, string $separatorChar = '-', int $separatorCharsCount = 3)
    {
        $separator = '';
        $smallSeparator = '';

        for ($i = 0 ; $i < $separatorCharsCount ; $i++) {
            $smallSeparator .= $separatorChar;
        }

        $stringLength = strlen($text) + 2 * strlen($smallSeparator) + 2;

        for ($i = 0 ; $i < $stringLength ; $i++) {
            $separator .= $separatorChar;
        }

        $this->writeInfo($separator);
        $this->writeInfo(sprintf('%s %s %s', $smallSeparator, $text, $smallSeparator));
        $this->writeInfo($separator);

        return $this;
    }

    /**
     * @param string $text
     * @param bool   $newLine
     * @return $this
     */
    public function writeInfo(string $text, bool $newLine = true): self
    {
        return $this->write($text, $newLine, self::COLOR_CYAN);
    }

    /**
     * @param string $text
     * @param bool   $newLine
     * @return $this
     */
    public function writeSuccess(string $text, bool $newLine = true): self
    {
        return $this->write($text, $newLine, self::COLOR_GREEN);
    }

    /**
     * @param string $text
     * @param bool   $newLine
     * @return $this
     */
    public function writeError(string $text, bool $newLine = true): self
    {
        return $this->write($text, $newLine, self::COLOR_RED);
    }

    /**
     * @param string $text
     * @param bool   $newLine
     * @return $this
     */
    public function writeWarning(string $text, bool $newLine = true): self
    {
        return $this->write($text, $newLine, self::COLOR_YELLOW);
    }

    /**
     * @param string $text
     * @param mixed  $color
     * @param mixed  $backgroundColor
     * @return string
     */
    private function getColoredText(string $text, $color = null, $backgroundColor = null): string
    {
        return sprintf("%s[%sm%s%s[0m", chr(27), $color, $text, chr(27));
    }

    /**
     * @throws \Exception
     * @param string $commandName
     * @return AbstractModuleCommand
     */
    private function searchCommand(string $commandName)
    {
        foreach ($this->commands as $category => $commands) {
            /** @var $commands AbstractModuleCommand[] */
            foreach ($commands as $command) {
                if ($command->name == $commandName) {
                    return $command;
                }
            }
        }

        throw new \Exception(sprintf('There is no registered command: %s', $commandName));
    }

    /**
     * @param string $signature
     * @param string $command
     * @param array  $params
     * @return array
     */
    private function parseParams(string $signature, string $command, array $params = []): array
    {
        var_dump($params); die;
    }

}
