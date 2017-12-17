<?php

namespace Bleidd\View;

use Bleidd\Application\App;
use Bleidd\Application\Application;

/** @method url(string $route, array $parameters = []) */
/** @method lang(string $key, string $langCode = null) */
final class ViewRenderer
{

    /** @var AbstractViewModel */
    private $viewModel;

    /** @var AssetManager */
    private $assetManager;

    /**
     * ViewRenderer constructor
     */
    public function __construct()
    {
        $this->assetManager = App::make(AssetManager::class);
    }

    /**
     * @param AbstractViewModel $viewModel
     * @return self
     */
    public function setViewModel(AbstractViewModel $viewModel): self
    {
        $this->viewModel = $viewModel;
        return $this;
    }

    /**
     * @param string $name
     * @param array  $arguments
     * @return string
     */
    public function __call(string $name, array $arguments)
    {
        $class = sprintf('%s\Plugin\%s', str_replace('\ViewRenderer', '', get_class($this)), ucfirst($name));

        if (!class_exists($class)) {
            return sprintf('Class %s does not exist!', $class);
        }

        $object = new $class;
        return $object($arguments);
    }

    /**
     * @return AssetManager
     */
    public function assets(): AssetManager
    {
        return $this->assetManager;
    }

    /**
     * @param bool $returnHtml
     * @return void
     */
    public function render(bool $returnHtml = false)
    {
        $this->viewModel->prepare();

        ob_start();
        extract($this->viewModel->getVariables());
        require $this->viewModel->getTemplate();
        $content = ob_get_contents();
        ob_end_clean();

        if ($this->viewModel->isTerminal()) {
            if ($returnHtml) {
                return $content;
            }

            echo $content;
            return;
        }

        require $this->viewModel->getLayout();
    }

}
