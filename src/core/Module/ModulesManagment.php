<?php

namespace Bleidd\Module;

use Bleidd\Util\FileReader;
use Bleidd\Util\Inflector;
use Bleidd\Application\App;
use Bleidd\Application\Runtime;
use Bleidd\Application\Application;
use Bleidd\Module\Events\ModuleBooted;

final class ModulesManagment
{

    /** @var array */
    protected $modules = [];
    
    private function __clone() {}
    private function __wakeup() {}

    /**
     * ModulesManagment constructor
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {

    }
    
    /**
     *
     */
    public function loadModules()
    {
        $modulesPath = Runtime::config()->configKey('modules_path');
        
        if (!is_dir($modulesPath)) {
            return;
        }
        
        foreach (scandir($modulesPath) as $module) {
            if (in_array($module, ['.', '..'])) {
                continue;
            }
            
            $modulePath = realpath(sprintf('%s/%s', $modulesPath, $module));
            
            $this->registerModule($modulePath);
        }
    }

    /**
     * @throws \Exception
     * @param string $modulePath
     */
    private function registerModule(string $modulePath)
    {
        $moduleFile = sprintf('%s%ssrc%sModule.php', $modulePath, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
        
        if (!file_exists($moduleFile)) {
            return;
        }
        
        $moduleClass = $this->getModuleClassNamespace($moduleFile);
        
        /** @var $module AbstractModuleProvider */
        $module = App::make($moduleClass);
        
        $module->boot();

        $moduleName = $module->name;
        $moduleDir = dirname(dirname($moduleFile));
        $configFile = sprintf('%s/config/config.php', $moduleDir);

        if (file_exists($configFile)) {
            Runtime::config()->addModuleConfig([
                Inflector::to_underscore($moduleName) => include $configFile
            ]);
        }
        
        $languageFilesDir = sprintf('%s/resources/lang', $moduleDir);
        
        foreach ((new FileReader())->readLocation($languageFilesDir) as $entry) {
            $langFilePath = sprintf('%s/%s', $languageFilesDir, $entry);
            $langCode = str_replace('.php', '', $entry);
            Runtime::language()->addModuleLangs(Inflector::to_underscore($moduleName), $langCode, include $langFilePath);
        }

        Runtime::dispatcher()->fire(new ModuleBooted($moduleClass, $moduleName, $moduleDir, $this->modules));

        $this->modules[$moduleClass] = [
            'name' => $moduleName,
            'path' => $moduleDir,
        ];
    }
    
    /**
     * @param $moduleFile
     * @return string
     */
    private function getModuleClassNamespace($moduleFile): string
    {
        $fp = fopen($moduleFile, 'r');
        $class = $namespace = $buffer = '';
        $i = 0;
        
        while (!$class) {
            if (feof($fp)) {
                break;
            }
        
            $buffer .= fread($fp, 512);
            $tokens = token_get_all($buffer);
        
            if (strpos($buffer, '{') === false) {
                continue;
            }
        
            for (; $i < count($tokens) ; $i++) {
                if ($tokens[$i][0] === T_NAMESPACE) {
                    for ($j = $i + 1 ; $j < count($tokens) ; $j++) {
                        if ($tokens[$j][0] === T_STRING) {
                            $namespace .= (!empty($namespace) ? '\\' : '') . $tokens[$j][1];
                        } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                            break;
                        }
                    }
                }
            
                if ($tokens[$i][0] === T_CLASS) {
                    for ($j = $i + 1 ; $j < count($tokens) ; $j++) {
                        if ($tokens[$j] === '{') {
                            $class = $tokens[$i + 2][1];
                        }
                    }
                }
            }
        }
        
        return sprintf('%s\%s', $namespace, $class);
    }

}
