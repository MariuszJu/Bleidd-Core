<?php

namespace Bleidd\Form\Traits;

trait AttributesTrait
{

    /** @var array */
    protected $attributes = [];

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setAttribute(string $name, $value): self
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setAttributeIfNotExists(string $name, $value): self
    {
        if (!$this->hasAttribute($name)) {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function setAttributes(array $values): self
    {
        $this->attributes = $values;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $fallbackValue
     * @return mixed
     */
    public function getAttribute(string $name, $fallbackValue = null)
    {
        if ($this->hasAttribute($name)) {
            return $this->attributes[$name];
        }

        $this->setAttribute($name, $fallbackValue);

        return $this->getAttribute($name);
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function removeAttribute(string $name): self
    {
        if ($this->hasAttribute($name)) {
            unset($this->attributes[$name]);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return $this
     */
    public function addAttribute(string $name, $value): self
    {
        if ($this->hasAttribute($name)) {
            $this->attributes[$name] .= ' ' . $value;
        } else {
            $this->attributes[$name] = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function stringifyAttributes(): string
    {
        $attributesString = '';

        foreach ($this->attributes as $attribute => $value) {
            $attributesString .= sprintf('%s = "%s" ', $attribute, $value);
        }

        return trim($attributesString);
    }

}