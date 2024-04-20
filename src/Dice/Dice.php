<?php

namespace Narwhal\Dice;

// namespace Narwhal\Dice\Dice;

class Dice
{
    protected ?int $value;
    // private int $lastRoll;

    // public function __construct($sides = 6) {
    //     $this->value = random_int(1, $sides);
    // }

    public function __construct()
    {
        $this->value = null;
    }

    // public function roll() {
    //     $this->value = random_int(1,6);
    //     $this->lastRoll = $this->value;
    //     return $this->value;
    // }

    public function roll(): int
    {
        $this->value = random_int(1, 6);
        return $this->value;
    }

    public function printValue()
    {
        echo $this->value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getAsString(): string
    {
        return "[{$this->value}]";
    }

    public function getLastRoll()
    {
        return $this->lastRoll;
    }

}
