<?php

namespace synapsenet\entity;

use synapsenet\math\Vector3;

class Entity
{
    protected $name;
    protected $health;
    protected $maxHealth;
    protected $position;
    protected $isInvisible;
    protected $speed;
    protected $isFlying;

    public function __construct(string $name, int $maxHealth)
    {
        $this->name = $name;
        $this->maxHealth = $maxHealth;
        $this->health = $maxHealth;
        $this->position = new Vector3(0, 0, 0);
        $this->isInvisible = false;
        $this->speed = 1.0;
        $this->isFlying = false;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    public function setHealth(int $health): void
    {
        $this->health = max(0, min($health, $this->maxHealth));
    }

    public function getMaxHealth(): int
    {
        return $this->maxHealth;
    }

    public function setMaxHealth(int $maxHealth): void
    {
        $this->maxHealth = max(1, $maxHealth);
        $this->health = min($this->health, $this->maxHealth);
    }

    public function getPosition(): Vector3
    {
        return $this->position;
    }

    public function setPosition(Vector3 $position): void
    {
        $this->position = $position;
    }

    public function isInvisible(): bool
    {
        return $this->isInvisible;
    }

    public function setInvisible(bool $isInvisible): void
    {
        $this->isInvisible = $isInvisible;
    }

    public function getSpeed(): float
    {
        return $this->speed;
    }

    public function setSpeed(float $speed): void
    {
        $this->speed = max(0.0, $speed);
    }

    public function isFlying(): bool
    {
        return $this->isFlying;
    }

    public function setFlying(bool $isFlying): void
    {
        $this->isFlying = $isFlying;
    }

    public function heal(int $amount): void
    {
        $this->health = min($this->health + $amount, $this->maxHealth);
    }

    public function takeDamage(int $amount): void
    {
        $event = new EntityDamageEvent($this, $damage);
    
        $finalDamage = $event->getDamage();
        $this->health -= $finalDamage;
        
        if ($this->health <= 0) {
            $this->health = 0;
            $this->onDeath();
        }
    }

    public function move(Vector3 $destination): void
    {
        $this->position = $destination;
    }

    public function attack(Entity $target): void
    {
        $target->takeDamage(10);
    }

    public function getDistanceTo(Entity $entity): float
    {
        $dx = $entity->getPosition()->getX() - $this->position->getX();
        $dy = $entity->getPosition()->getY() - $this->position->getY();
        $dz = $entity->getPosition()->getZ() - $this->position->getZ();

        return sqrt($dx * $dx + $dy * $dy + $dz * $dz);
    }

    public function isInRange(Entity $entity, float $range): bool
    {
        return $this->getDistanceTo($entity) <= $range;
    }

    public function teleport(Vector3 $position): void
    {
        $this->position = $position;
    }
}
