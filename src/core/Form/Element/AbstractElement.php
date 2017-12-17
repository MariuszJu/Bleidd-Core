<?php

namespace Bleidd\Form\Element;

use Bleidd\Application\Runtime;
use Bleidd\Form\Traits\AttributesTrait;

abstract class AbstractElement
{

    use AttributesTrait;

    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $type;

    /** @var string|array */
    protected $value;

    /** @var string */
    protected $label;

    /** @var array */
    protected $messages = [];

    /** @var string */
    protected $prependHtml = '';

    /** @var string */
    protected $appendHtml = '';

    /**
     * AbstractElement constructor
     *
     * @param string $name
     * @param array  $attributes
     */
    public function __construct(string $name, array $attributes = [])
    {
        $this->name = $name;
        $this->attributes = $attributes;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label ?? '';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @return string
     */
    protected function render(): string
    {
        return Runtime::view(sprintf('form.element.%s', $this->type), [
            'element' => $this
        ])->render(true);
    }

}
