<?php

namespace Bleidd\Util;

use Bleidd\Application\Application;

final class Config
{
    
    /**
     * @var array
     */
    private $config;
    
    private function __clone() {}
    private function __wakeup() {}

    /**
     * Config constructor
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->loadConfig();
    }
    
    /**
     * Load config file
     *
     * @throws \Exception
     * @return $this
     */
    private function loadConfig(): Config
    {
        $configFile = sprintf('%s/config/app.php', ROOT_DIR);
    
        if (!file_exists($configFile)) {
            throw new \Exception('No config file is available. Exiting...');
        }
        
        $this->config = require $configFile;
        
        return $this;
    }

    /**
     * @param array $config
     */
    public function addModuleConfig(array $config)
    {
        $this->config = array_merge($config, $this->config);
    }

    /**
     * Get config by key
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function configKey(string $key, $default = null)
    {
        $keys = strpos($key, '.') !== false ? explode('.', $key) : [$key];

        $index = 0;
        $found = true;
        $currentConfig = $this->config;
        do {
            $key = $keys[$index++];
        
            if (is_array($currentConfig) && isset($currentConfig[$key])) {
                $currentConfig = $currentConfig[$key];
            } else {
                $found = false;
                break;
            }
        
        } while ($index < count($keys));
    
        return $found ? $currentConfig : $default;
    }
    
}
