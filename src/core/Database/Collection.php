<?php

namespace Bleidd\Database;

use Bleidd\Database\Entity\AbstractEntity;

final class Collection implements \Iterator
{

    /** @var AbstractEntity[] */
    private $elements;

    /**
     * Collection constructor
     */
    public function __construct()
    {
        $this->elements = [];
    }

    /**
     * @return AbstractEntity
     */
    public function current()
    {
        return current($this->elements);
    }

    /**
     * @return AbstractEntity
     */
    public function next(): AbstractEntity
    {
        return next($this->elements);
    }

    /**
     * @return int
     */
    public function key(): int
    {
        return key($this->elements);
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return key($this->elements) !== null;
    }

    /**
     * @return AbstractEntity|null
     */
    public function rewind()
    {
        return reset($this->elements);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * @param AbstractEntity $entity
     * @return self
     */
    public function add(AbstractEntity $entity): self
    {
        $this->elements[] = $entity;
        return $this;
    }

    /**
     * @return AbstractEntity|null
     */
    public function first()
    {
        return $this->rewind();
    }

    /**
     * @return AbstractEntity|null
     */
    public function last()
    {
        return end($this->elements);
    }

}
