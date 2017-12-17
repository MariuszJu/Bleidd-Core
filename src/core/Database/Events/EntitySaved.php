<?php

namespace Bleidd\Database\Events;

use Bleidd\Event\AbstractEvent;
use Bleidd\Database\Entity\AbstractEntity;

class EntitySaved extends AbstractEvent
{
    
    /** @var string */
    public static $name = 'Entity Saved';
    
    /** @var AbstractEntity */
    public $entity;
    
    /**
     * EntitySaved constructor
     *
     * @param AbstractEntity $entity
     */
    public function __construct(AbstractEntity $entity)
    {
        $this->entity = $entity;
    }
    
}
