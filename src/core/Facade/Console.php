<?php

namespace Bleidd\Facade;

use Bleidd\Application\Runtime;

final class Console
{

    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    /**
     * @throws \Exception
     * @param string $text
     * @param bool   $newLine
     * @param mixed  $color
     * @param mixed  $backgroundColor
     * @return \Bleidd\Console\Console
     */
    public static function write(string $text, bool $newLine = true, $color = null, $backgroundColor = null): \Bleidd\Console\Console
    {
        return Runtime::console()->write($text, $newLine, $color, $backgroundColor);
    }

    /**
     * @throws \Exception
     * @param string $text
     * @param bool   $newLine
     * @return \Bleidd\Console\Console
     */
    public static function writeInfo(string $text, bool $newLine = true): \Bleidd\Console\Console
    {
        return Runtime::console()->write($text, $newLine, \Bleidd\Console\Console::COLOR_CYAN);
    }

    /**
     * @throws \Exception
     * @param string $text
     * @param bool   $newLine
     * @return \Bleidd\Console\Console
     */
    public static function writeSuccess(string $text, bool $newLine = true): \Bleidd\Console\Console
    {
        return Runtime::console()->write($text, $newLine, \Bleidd\Console\Console::COLOR_GREEN);
    }

    /**
     * @throws \Exception
     * @param string $text
     * @param bool   $newLine
     * @return \Bleidd\Console\Console
     */
    public static function writeError(string $text, bool $newLine = true): \Bleidd\Console\Console
    {
        return Runtime::console()->write($text, $newLine, \Bleidd\Console\Console::COLOR_RED);
    }

    /**
     * @throws \Exception
     * @param string $text
     * @param bool   $newLine
     * @return \Bleidd\Console\Console
     */
    public static function writeWarning(string $text, bool $newLine = true): \Bleidd\Console\Console
    {
        return Runtime::console()->write($text, $newLine, \Bleidd\Console\Console::COLOR_YELLOW);
    }

    /**
     * @throws \Exception
     * @param string      $text
     * @param string|null $description
     * @return \Bleidd\Console\Console
     */
    public static function writeHeading(string $text, string $description = null): \Bleidd\Console\Console
    {
        return Runtime::console()->writeHeading($text, $description);
    }

    /**
     * @throws \Exception
     * @param string $text
     * @param string $separatorChar
     * @param int    $separatorCharsCount
     * @return \Bleidd\Console\Console
     */
    public static function writeCustomHeading(string $text, string $separatorChar = '-', int $separatorCharsCount = 3): \Bleidd\Console\Console
    {
        return Runtime::console()->writeCustomHeading($text, $separatorChar, $separatorCharsCount);
    }

}
