<?php

namespace synapsenet\event\entity;

use synapsenet\event\Event;
use synapsenet\entity\Entity;

abstract class EntityEvent extends Event
{
    protected $entity;
    
    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }
    
    public function getEntity(): Entity
    {
        return $this->entity;
    }
}
