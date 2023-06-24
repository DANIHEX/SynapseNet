<?php

namespace synapsenet\event\entity;

use synapsenet\event\Event;
use synapsenet\entity\Entity;

abstract class EntityEvent extends Event {

    /** @var Entity */
    protected Entity $entity;

    /**
     * @param Entity $entity
     */
    public function __construct(Entity $entity) {
        $this->entity = $entity;
    }

    /**
     * @return Entity
     */
    public function getEntity(): Entity {
        return $this->entity;
    }
}
