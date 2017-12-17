<?php

namespace Bleidd\View;

use Bleidd\Util\FileReader;

final class AssetManager
{

    /** @var array */
    private $scripts = [];
    
    /** @var array */
    private $styleSheets = [];

    /** @var array */
    private $placeholders = [];

    /** @var string */
    private $scriptFormat = '<script src="%s"></script>';

    /** @var string */
    private $styleSheetFormat = '<link href="%s" media="screen" rel="stylesheet" type="text/css"/>';

    /**
     * @param string $resource
     * @return $this
     */
    public function append(string $resource): self
    {
        return strpos($resource, '.js') !== false
            ? $this->appendScript($resource)
            : $this->appendStyleSheet($resource);
    }

    /**
     * @param string $resource
     * @return $this
     */
    public function prepend(string $resource): self
    {
        return strpos($resource, '.js') !== false
            ? $this->prependScript($resource)
            : $this->prependStyleSheet($resource);
    }

    /**
     * @param string $script
     * @return $this
     */
    public function appendScript(string $script): self
    {
        $this->scripts[] = $this->getRelativeAssetPath($script);
        return $this;
    }

    /**
     * @param string $script
     * @return $this
     */
    public function prependScript(string $script): self
    {
        array_unshift($this->scripts, $this->getRelativeAssetPath($script));
        return $this;
    }

    /**
     * @param string $styleSheet
     * @return $this
     */
    public function appendStyleSheet(string $styleSheet): self
    {
        $this->styleSheets[] = $this->getRelativeAssetPath($styleSheet);
        return $this;
    }

    /**
     * @param string $styleSheet
     * @return $this
     */
    public function prependStyleSheet(string $styleSheet): self
    {
        array_unshift($this->styleSheets, $this->getRelativeAssetPath($styleSheet));
        return $this;
    }

    /**
     * @param string $outputStyleSheets
     * @return $this
     */
    public function minifyStylesheets(string $outputStyleSheets): self
    {
        $this->createMinifiedFile($outputStyleSheets, $this->styleSheets);
        $this->styleSheets = [$this->getRelativeAssetPath($outputStyleSheets)];
        return $this;
    }

    /**
     * @param string $outputScripts
     * @return AssetManager
     */
    public function minifyScripts(string $outputScripts): self
    {
        $this->createMinifiedFile($outputScripts, $this->scripts);
        $this->scripts = [$this->getRelativeAssetPath($outputScripts)];
        return $this;
    }

    /**
     * @return $this
     */
    public function minify(): self
    {
        $reader = new FileReader();

        foreach (array_merge($this->styleSheets, $this->scripts) as $resource) {
            $filePath = sprintf('%s/%s', ROOT_DIR, $resource);
            $reader->init($filePath, FileReader::MODE_READ);
            $minifiedContent = $this->getMinifiedContent($reader->content());
            $reader->closeFile();

            $reader->init($filePath,FileReader::MODE_WRITE_CREATE);
            $reader->write($minifiedContent);
            $reader->closeFile();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function renderScripts(): string
    {
        $scriptsHtml = '';

        foreach ($this->scripts as $script) {
            $scriptsHtml .= sprintf($this->scriptFormat, $script) . PHP_EOL;
        }

        return $scriptsHtml;
    }

    /**
     * @return string
     */
    public function renderStyleSheets(): string
    {
        $styleSheetsHtml = '';

        foreach ($this->styleSheets as $styleSheet) {
            $styleSheetsHtml .= sprintf($this->styleSheetFormat, $styleSheet) . PHP_EOL;
        }

        return $styleSheetsHtml;
    }

    /**
     * @return string
     */
    public function publicPath(): string
    {
        return $this->getRelativeAssetPath('');
    }

    /**
     * @return void
     */
    public function placeholderStart()
    {
        ob_start();
    }

    /**
     * @return void
     */
    public function placeholderEnd()
    {
        $this->placeholders[] = ob_get_contents();
        ob_end_clean();
    }

    /**
     * @return string
     */
    public function renderPlaceholders(): string
    {
        return implode(PHP_EOL, $this->placeholders);
    }

    /**
     * @param string $outputFile
     * @param array  $resources
     */
    private function createMinifiedFile(string $outputFile, array $resources = [])
    {
        $reader = new FileReader();
        $minifiedFiles = [];

        foreach ($resources as $resource) {
            $filePath = sprintf('%s/%s', ROOT_DIR, $resource);
            $reader->init($filePath, FileReader::MODE_READ);
            $minifiedFiles[] = $this->getMinifiedContent($reader->content());
            $reader->closeFile();
        }

        $reader->init($this->getAbsoluteAssetPath($outputFile),FileReader::MODE_WRITE_CREATE);
        $reader->write(implode(PHP_EOL, $minifiedFiles));
        $reader->closeFile();
    }

    /**
     * @param string $file
     * @return string
     */
    private function getRelativeAssetPath(string $file): string
    {
        return sprintf('/public/assets/%s', $file);
    }

    /**
     * @param string $file
     * @return string
     */
    private function getAbsoluteAssetPath(string $file): string
    {
        return sprintf('%s%s', ROOT_DIR, $this->getRelativeAssetPath($file));
    }

    /**
     * @param string $content
     * @return string
     */
    private function getMinifiedContent(string $content)
    {
        return str_replace(
            ["\n", "\r"], ' ', preg_replace('/\s+/', ' ',$content
        ));
    }

}
