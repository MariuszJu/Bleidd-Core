<?php

namespace Bleidd\View;

abstract class AbstractViewModel
{

    /** @var array */
    protected $variables;

    /**
     * AbstractViewModel constructor
     */
    public function __construct()
    {
        $this->variables = [];
    }

    /**
     * @return self
     */
    abstract function prepare();

    /**
     * @return string
     */
    abstract function getTemplate(): string;

    /**
     * @return string
     */
    abstract function getLayout(): string;

    /**
     * @return bool
     */
    abstract function isTerminal(): bool;

    /**
     * @param $name
     * @param $value
     * @return self
     */
    public function setVariable($name, $value): self
    {
        if (preg_match('/^[a-zA-Z]+[a-zA-Z0-9_]+$/', $name, $matches)) {
            $this->variables[$name] = $value;
        }

        return $this;
    }

    /**
     * @param array $variables
     * @return self
     */
    public function setVariables(array $variables): self
    {
        foreach ($variables as $name => $value) {
            $this->setVariable($name, $value);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

}
