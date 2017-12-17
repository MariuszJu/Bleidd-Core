<?php

namespace Bleidd\Form\Traits;

use Bleidd\Form\Element\AbstractElement;

trait FormElementsTrait
{

    /** @var AbstractElement[] */
    protected $elements = [];

    /**
     * @param AbstractElement $element
     * @return self
     */
    public function addElement(AbstractElement $element): self
    {
        $this->elements[$element->getName()] = $element;
        return $this;
    }

    /**
     * @param array $elements
     * @return self
     */
    public function addElements(array $elements): self
    {
        $this->elements += $elements;
        return $this;
    }

    /**
     * @return AbstractElement[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @param string $name
     * @return AbstractElement|null
     */
    public function getElement(string $name): ?AbstractElement
    {
        return $this->elements[$name] ?? null;
    }

    /**
     * @param string $name
     * @return AbstractElement|null
     */
    public function getElementByName(string $name): ?AbstractElement
    {
        foreach ($this->getElements() as $element) {
            if ($element->getName() == $name) {
                return $element;
            }
        }

        return null;
    }

}
