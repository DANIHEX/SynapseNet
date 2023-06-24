<?php

namespace synapsenet\math;

class Vector3 {

    /** @var int|float */
    public int|float $x;
    public int|float $y;
    public int|float $z;

    /**
     * @param float $x
     * @param float $y
     * @param float $z
     */
    public function __construct(float $x = 0, float $y = 0, float $z = 0) {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    /**
     * @return float|int
     */
    public function getX(): float|int {
        return $this->x;
    }

    /**
     * @return float|int
     */
    public function getY(): float|int {
        return $this->y;
    }

    /**
     * @return float|int
     */
    public function getZ(): float|int {
        return $this->z;
    }

    /**
     * @param float $x
     * @param float $y
     * @param float $z
     *
     * @return void
     */
    public function setComponents(float $x, float $y, float $z): void {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    /**
     * @param Vector3 $v
     *
     * @return Vector3
     */
    public function add(Vector3 $v): Vector3 {
        return new Vector3($this->x + $v->x, $this->y + $v->y, $this->z + $v->z);
    }

    /**
     * @param Vector3 $v
     *
     * @return Vector3
     */
    public function subtract(Vector3 $v): Vector3 {
        return new Vector3($this->x - $v->x, $this->y - $v->y, $this->z - $v->z);
    }

    /**
     * @param float $scalar
     *
     * @return Vector3
     */
    public function multiply(float $scalar): Vector3 {
        return new Vector3($this->x * $scalar, $this->y * $scalar, $this->z * $scalar);
    }

    /**
     * @param float $scalar
     *
     * @return Vector3
     */
    public function divide(float $scalar): Vector3 {
        if($scalar !== 0) {
            return new Vector3($this->x / $scalar, $this->y / $scalar, $this->z / $scalar);
        }

        return new Vector3();
    }

    /**
     * @return Vector3
     */
    public function floor(): Vector3 {
        return new Vector3(floor($this->x), floor($this->y), floor($this->z));
    }

    /**
     * @return Vector3
     */
    public function ceil(): Vector3 {
        return new Vector3(ceil($this->x), ceil($this->y), ceil($this->z));
    }

    /**
     * @return Vector3
     */
    public function round(): Vector3 {
        return new Vector3(round($this->x), round($this->y), round($this->z));
    }
}
