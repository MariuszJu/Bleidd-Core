<?php

namespace Bleidd\Language;

use Bleidd\Session\Session;
use Bleidd\Application\Runtime;
use Bleidd\Application\Application;

final class Language
{

    /** @var string|null */
    private $langCode;

    /** @var Session */
    private $session;

    /** @var array */
    private $langs = [];

    /** @var string */
    private $sessionLangCodeKey = 'app_lang_code';

    private function __clone() {}
    private function __wakeup() {}

    /**
     * Language constructor
     *
     * @param Application $application
     * @param Session $session
     */
    public function __construct(Application $application, Session $session)
    {
        $this->session = $session;
        $this->init();
    }

    /**
     * @return self
     */
    private function init(): self
    {
        $this->setLanguage(
            ($langCode = $this->session->get($this->sessionLangCodeKey)) ?
            $langCode : Runtime::config()->configKey('language.default')
        );

        return $this;
    }

    /**
     * @param string $langCode
     * @return self
     */
    public function setLanguage(string $langCode): self
    {
        $this->langCode = $langCode;
        $this->session->set($this->sessionLangCodeKey, $langCode);

        return $this;
    }

    /**
     * @param string $module
     * @param string $langCode
     * @param array  $langs
     */
    public function addModuleLangs(string $module, string $langCode, array $langs)
    {
        $this->langs[$module][$langCode] = $langs;
    }

    /**
     * @param string      $key
     * @param string|null $langCode
     * @return string
     */
    public function translate(string $key, string $langCode = null): string
    {
        if (empty($langCode)) {
            $langCode = $this->langCode;
        }

        if (strpos($key, '.') === false) {
            return $this->langs[$langCode][$key] ?? $key;
        }

        $keys = explode('.', $key);

        $index = 1;
        $found = true;
        $currentConfig = $this->langs[$keys[0]][$langCode];

        do {
            $arg = $keys[$index++];

            if (is_array($currentConfig) && isset($currentConfig[$arg])) {
                $currentConfig = $currentConfig[$arg];
            } else {
                $found = false;
                break;
            }

        } while ($index < count($keys));

        return $found ? $currentConfig : $key;
    }

    /**
     * @param array       $array
     * @param string      $prefix
     * @param bool        $assoc
     * @param string|null $langCode
     * @return array
     */
    public function translateArray(array $array, string $prefix = '', bool $assoc = false, string $langCode = null): array
    {
        $translated = [];

        foreach ($array as $key => $value) {
            $translated[$assoc ? $value : $key] = $this->translate(
                !empty($prefix) ? sprintf('%s.%s', $prefix, $value) : $value, $langCode
            );
        }

        return $translated;
    }

}
