<?php

namespace Bleidd\View;

use Bleidd\Util\FileReader;
use Bleidd\Application\Runtime;

class ViewModel extends AbstractViewModel
{

    /** @var string */
    private $template;

    /** @var bool */
    private $isTerminal;

    /** @var string */
    private $layout;

    /** @var array */
    private $allowedTemplateExtensions = ['php', 'phtml'];

    /** @var string */
    private $controllerClass;

    /** @var string */
    private $actionName;

    /**
     * ViewModel constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->isTerminal = false;
    }

    /**
     * @param string $controllerClass
     * @return self
     */
    public function setControllerClass(string $controllerClass): self
    {
        $this->controllerClass = $controllerClass;
        return $this;
    }

    /**
     * @param string $actionName
     * @return self
     */
    public function setActionName(string $actionName): self
    {
        $this->actionName = $actionName;
        return $this;
    }

    /**
     * @return self
     */
    public function prepare(): self
    {
        $this->resolveTemplate($this->controllerClass, $this->actionName);
        return $this;
    }

    /**
     * @throws \Exception
     * @param string $moduleName
     */
    private function resolveLayout(string $moduleName)
    {
        if (!empty($layout = Runtime::config()->configKey('view.layout', null))) {
            $this->layout = $layout;
            return;
        }

        $layoutPath = sprintf('%s/src/app/%s/resources/views/layout',
            ROOT_DIR, $moduleName
        );

        if (!is_dir($layoutPath)) {
            throw new \Exception(sprintf('Could not find any layout file'));
        }
        
        foreach ((new FileReader())->readLocation($layoutPath) as $entry) {
            $filePath = sprintf('%s/%s', $layoutPath, $entry);
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            
            if (in_array($ext, $this->allowedTemplateExtensions)) {
                $this->layout = $filePath;
                return;
            }
        }

        if (!is_dir($layoutPath)) {
            throw new \Exception(sprintf('Could not find aby layout file'));
        }
    }

    /**
     * @throws \Exception
     * @param $controllerClass
     * @param $actionName
     */
    private function resolveTemplate(string $controllerClass, string $actionName)
    {
        $templateFileName = strtolower(str_replace('Action', '', $actionName));
        $namespaceParts = explode('\\', $controllerClass);
        $controller = end($namespaceParts);
        $moduleName = $namespaceParts[2];

        $viewPath = sprintf('%s/src/app/%s/resources/views/%s',
            ROOT_DIR, $moduleName, strtolower(str_replace('Controller', '', $controller))
        );

        if (!is_dir($viewPath)) {
            throw new \Exception(sprintf('Directory %s does not exist', $viewPath));
        }

        foreach ($this->allowedTemplateExtensions as $allowedTemplateExtension) {
            $file = sprintf('%s/%s.%s', $viewPath, $templateFileName, $allowedTemplateExtension);

            if (file_exists($file)) {
                $this->template = $file;
            }
        }

        if (empty($this->layout)) {
            $this->resolveLayout($moduleName);
        }

        if (empty($this->template)) {
            throw new \Exception(sprintf('There is no template file for action %s', $actionName));
        }
    }

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     * @return $this
     */
    public function setLayout(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasTemplate(): bool
    {
        return !empty($this->template);
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     * @return self
     */
    public function setTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @param $isTerminal
     * @return self
     */
    public function setIsTerminal($isTerminal): self
    {
        $this->isTerminal = (bool) $isTerminal;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTerminal(): bool
    {
        return $this->isTerminal;
    }

}
