<?php

namespace Bleidd\Database\Entity;

use Bleidd\Util\Inflector;

abstract class AbstractEntity implements \JsonSerializable
{

    /**
     * @return array
     */
    abstract public function getVirtualColumns(): array;

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * AbstractEntity constructor
     */
    public function __construct()
    {

    }

    /**
     * @param array $data
     * @return self
     */
    public function fill(array $data = []): self
    {
        foreach ($data as $key => $value) {
            $modelKey = Inflector::toCamelCase($key);
            $this->$modelKey = $value;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $fields = get_object_vars($this);
        $newFields = array();

        foreach($fields as $fieldName => $fieldValue) {
            $newFieldName = Inflector::to_underscore($fieldName);
            $newFields[$newFieldName] = $this->$fieldName;
        }

        return $newFields;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return empty($this->id);
    }

    /**
     * @return bool
     */
    public function hasId(): bool
    {
        return !$this->isNew();
    }

    /**
     * @return string
     */
    public function getEntitySlug(): string
    {
        $class = get_class($this);
        $exploded = explode('\\', $class);

        return end($exploded);
    }

    /**
     * @return array
     */
    public function getArrayCopy(): array
    {
        $fields = get_object_vars($this);
        $newFields = [];

        foreach ($fields as $fieldName => $fieldValue) {
            $newFieldName = Inflector::to_underscore($fieldName);
            $newFields[$newFieldName] = $this->$fieldName;
        }

        return $newFields;
    }

    /**
     * @param array $data
     * @return self
     */
    public function exchangeArray(array $data = []): self
    {
        $fields = get_object_vars($this);

        foreach ($fields as $fieldName => $fieldValue) {
            $newFieldName = Inflector::to_underscore($fieldName);

            if (isset($data[$newFieldName])) {
                $this->$fieldName = $data[$newFieldName];
            } else {
                $this->$fieldName = null;
            }
        }

        return $this;
    }

}
