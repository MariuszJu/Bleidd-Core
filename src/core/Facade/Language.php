<?php

namespace Bleidd\Facade;

use Bleidd\Application\Runtime;

final class Language
{

    private function __clone() {}
    private function __wakeup() {}
    private function __construct() {}

    /**
     * @throws \Exception
     * @param string      $key
     * @param string|null $langCode
     * @return string
     */
    public static function translate(string $key, string $langCode = null)
    {
        return Runtime::language()
            ->translate($key, $langCode);
    }

    /**
     * @throws \Exception
     * @param array       $array
     * @param string      $prefix
     * @param bool        $assoc
     * @param string|null $langCode
     * @return array
     */
    public static function translateArray(array $array, string $prefix = '', bool $assoc = false, string $langCode = null): array
    {
        return Runtime::language()
            ->translateArray($array, $prefix, $assoc, $langCode);
    }

    /**
     * @throws \Exception
     * @param string $langCode
     * @return \Bleidd\Language\Language
     */
    public static function setLanguage(string $langCode): \Bleidd\Language\Language
    {
        return Runtime::language()
            ->setLanguage($langCode);
    }

}
