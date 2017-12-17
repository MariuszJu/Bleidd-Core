<?php

namespace Bleidd\View;

final class QuickViewModel extends AbstractViewModel
{

    /** @var string */
    private $template;

    /** @var string */
    private $layout;

    /** @var string */
    private $resourcesDir;

    /** @var array */
    private $allowedTemplateExtensions = ['php', 'phtml'];

    /**
     * QuickViewModel constructor
     *
     * @param string $template
     */
    public function __construct(string $template)
    {
        $this->resourcesDir = sprintf('%s/resources/views', ROOT_DIR);
        $this->setTemplate($template);
        parent::__construct();
    }

    /**
     * @return self
     */
    public function prepare(): self
    {
        return $this;
    }

    /**
     * @return bool
     */
    public function isTerminal(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return '';
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
    public function setTemplate($template): self
    {
        if (strpos($template, '.') !== false) {
            $template = str_replace('.', DIRECTORY_SEPARATOR, $template);
        }

        $template = sprintf('%s/%s', $this->resourcesDir, $template);
        
        foreach ($this->allowedTemplateExtensions as $allowedTemplateExtension) {
            $file = sprintf('%s.%s', $template, $allowedTemplateExtension);

            if (file_exists($file)) {
                $template = $file;
            }
        }

        $this->template = $template;

        return $this;
    }

}
