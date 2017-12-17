<?php

final class Autoloader
{

    /** @var array */
    private static $paths;
    
    /** @var string */
    private static $modulesPath;

    /** @var string */
    private static $baseNamespace = 'Bleidd';
    
    /** @var bool */
    private static $throwExceptions = true;

    /**
     * Init Autoloader
     */
    public static function init()
    {
        $configFile = sprintf('%s/config/app.php', __DIR__);

        if (!file_exists($configFile)) {
            exit('No config file is available. Exiting...');
        }
    
        $config = require $configFile;
        self::$paths = $config['autoloader_paths'] ?? [];
        self::$modulesPath = $config['modules_path'] ?? '';

        spl_autoload_register('static::loadClass');
    }

    /**
     * Load given class
     *
     * @throws \Exception
     * @param string $class
     * @return void
     */
    protected static function loadClass(string $class)
    {
        if (($pos = strpos($class, self::$baseNamespace . '\\')) !== false) {
            $class = substr($class, $pos + strlen(self::$baseNamespace) + 1);
        }

        $relativeFilePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $class) . '.php';
        
        foreach (self::$paths as $path) {
            if ($path == self::$modulesPath) {
                $relativeFilePath = str_replace('App' . DIRECTORY_SEPARATOR, '', $relativeFilePath);
                $parts = explode(DIRECTORY_SEPARATOR, $relativeFilePath);
                
                $relativeFilePath = $parts[0] . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array_slice($parts, 1));
            }
            
            if (substr($path, -1, 1) != '/') {
                $path .= '/';
            }

            $filePath = realpath($path . $relativeFilePath);
            
            if (file_exists($filePath)) {
                require_once $filePath;
                return;
            }
        }

        if (self::$throwExceptions) {
            throw new \Exception(sprintf('Could not find %s class', $class));
        }
    }

}

Autoloader::init();