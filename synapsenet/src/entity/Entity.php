<?php

namespace synapsenet\entity;

use synapsenet\math\Vector3;
use synapsenet\event\entity\EntityDamageEvent;

class Entity {

    /** @var string */
    protected string $name;

    /** @var int */
    protected int $health;
    /** @var int */
    protected int $maxHealth;

    /** @var Vector3 */
    protected Vector3 $position;

    /** @var bool */
    protected bool $isInvisible;

    /** @var float */
    protected float $speed;

    /** @var bool */
    protected bool $isFlying;

    /**
     * @param string $name
     * @param int $maxHealth
     */
    public function __construct(string $name, int $maxHealth) {
        $this->name = $name;
        $this->maxHealth = $maxHealth;
        $this->health = $maxHealth;
        $this->position = new Vector3(0, 0, 0);
        $this->isInvisible = false;
        $this->speed = 1.0;
        $this->isFlying = false;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getHealth(): int {
        return $this->health;
    }

    /**
     * @param int $health
     *
     * @return void
     */
    public function setHealth(int $health): void {
        $this->health = max(0, min($health, $this->maxHealth));
    }

    /**
     * @return int
     */
    public function getMaxHealth(): int {
        return $this->maxHealth;
    }

    /**
     * @param int $maxHealth
     *
     * @return void
     */
    public function setMaxHealth(int $maxHealth): void {
        $this->maxHealth = max(1, $maxHealth);
        $this->health = min($this->health, $this->maxHealth);
    }

    /**
     * @return bool
     */
    public function isInvisible(): bool {
        return $this->isInvisible;
    }

    /**
     * @param bool $isInvisible
     *
     * @return void
     */
    public function setInvisible(bool $isInvisible): void {
        $this->isInvisible = $isInvisible;
    }

    /**
     * @return float
     */
    public function getSpeed(): float {
        return $this->speed;
    }

    /**
     * @param float $speed
     *
     * @return void
     */
    public function setSpeed(float $speed): void {
        $this->speed = max(0.0, $speed);
    }

    /**
     * @return bool
     */
    public function isFlying(): bool {
        return $this->isFlying;
    }

    /**
     * @param bool $isFlying
     *
     * @return void
     */
    public function setFlying(bool $isFlying): void {
        $this->isFlying = $isFlying;
    }

    /**
     * @param int $amount
     *
     * @return void
     */
    public function heal(int $amount): void {
        $this->health = min($this->health + $amount, $this->maxHealth);
    }

    /**
     * @param Vector3 $destination
     *
     * @return void
     */
    public function move(Vector3 $destination): void {
        $this->position = $destination;
    }

    /**
     * @param Entity $target
     *
     * @return void
     */
    public function attack(Entity $target): void {
        $target->takeDamage(10);
    }

    /**
     * @param int $amount
     *
     * @return void
     */
    public function takeDamage(int $amount): void {
        $event = new EntityDamageEvent($this, $amount);

        $finalDamage = $event->getDamage();
        $this->health -= $finalDamage;

        if($this->health <= 0) {
            $this->health = 0;
            $this->onDeath();
        }
    }

    /**
     * @param Entity $entity
     * @param float $range
     *
     * @return bool
     */
    public function isInRange(Entity $entity, float $range): bool {
        return $this->getDistanceTo($entity) <= $range;
    }

    /**
     * @param Entity $entity
     *
     * @return float
     */
    public function getDistanceTo(Entity $entity): float {
        $dx = $entity->getPosition()->getX() - $this->position->getX();
        $dy = $entity->getPosition()->getY() - $this->position->getY();
        $dz = $entity->getPosition()->getZ() - $this->position->getZ();

        return sqrt($dx * $dx + $dy * $dy + $dz * $dz);
    }

    /**
     * @return Vector3
     */
    public function getPosition(): Vector3 {
        return $this->position;
    }

    /**
     * @param Vector3 $position
     *
     * @return void
     */
    public function setPosition(Vector3 $position): void {
        $this->position = $position;
    }

    /**
     * @param Vector3 $position
     *
     * @return void
     */
    public function teleport(Vector3 $position): void {
        $this->position = $position;
    }

    /**
     * @return void
     */
    private function onDeath(): void {
        // TODO
    }
}
