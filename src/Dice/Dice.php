<?php

namespace App\Dice;

// namespace Narwhal\Dice\Dice;

class Dice
{
    protected ?int $value;

    public function __construct()
    {
        $this->value = null;
    }

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
        if (is_int($this->value)) {
            return $this->value;
        }
        return 0;
    }

    public function getAsString(): string
    {
        return "[{$this->value}]";
    }

}
