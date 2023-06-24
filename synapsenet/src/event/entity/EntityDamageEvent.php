<?php

namespace synapsenet\event\entity;

use synapsenet\entity\Entity;

class EntityDamageEvent extends EntityEvent {

    /** @var float */
    private float $damage;

    /**
     * @param Entity $entity
     * @param float $damage
     */
    public function __construct(Entity $entity, float $damage) {
        parent::__construct($entity);

        $this->damage = $damage;
    }

    /**
     * @return float
     */
    public function getDamage(): float {
        return $this->damage;
    }

    /**
     * @param float $damage
     *
     * @return void
     */
    public function setDamage(float $damage): void {
        $this->damage = $damage;
    }
}