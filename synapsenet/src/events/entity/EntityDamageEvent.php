<?php

namespace synapsenet\event\entity;

use synapsenet\entity\Entity;
use synapsenet\event\Canceller;

class EntityDamageEvent extends EntityEvent implements Canceller
{
    private $damage;
    private $cancelled = false;
    
    public function __construct(Entity $entity, float $damage)
    {
        parent::__construct($entity);
        $this->damage = $damage;
    }
    
    public function getDamage(): float
    {
        return $this->damage;
    }
    
    public function setDamage(float $damage): void
    {
        $this->damage = $damage;
    }
    
    public function isCancelled(): bool
    {
        return $this->cancelled;
    }
    
    public function setCancelled(bool $cancelled): void
    {
        $this->cancelled = $cancelled;
    }
    
    public function getEventName(): string
    {
        return 'EntityDamageEvent';
    }

    public function callEvent(Canceller $canceller): void // im triping
    {
        if (!$this->isCancelled()) {
            $this->entity->onDamage($this->damage); // did i create this code???
        }
    }
}