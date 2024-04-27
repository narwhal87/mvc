<?php

namespace App\Card;

use App\Card\Card;
use App\Card\Deck;

class Hand extends Deck
{
    protected $hand = [];

    public function __construct()
    {
        $this->hand = $this->deck->draw(5);
    }
}
