<?php

namespace Narwhal\Card;

use Narwhal\Card\Card;
use Narwhal\Card\Deck;

class Hand extends Deck
{
    protected $hand = [];

    public function __construct() {
        $this->hand = $this->deck->draw(5);
    }

    // public function add(Dice $die): void
    // {
    //     $this->hand[] = $die;
    // }

    // public function roll(): void
    // {
    //     foreach ($this->hand as $die) {
    //         $die->roll();
    //     }
    // }

    // public function getNumberDices(): int
    // {
    //     return count($this->hand);
    // }

    // public function getValues(): array
    // {
    //     $values = [];
    //     foreach ($this->hand as $die) {
    //         $values[] = $die->getValue();
    //     }
    //     return $values;
    // }

    // public function getString(): array
    // {
    //     $values = [];
    //     foreach ($this->hand as $die) {
    //         $values[] = $die->getAsString();
    //     }
    //     return $values;
    // }
}
