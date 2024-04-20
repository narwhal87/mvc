<?php

namespace Narwhal\Card;

use Narwhal\Card\Card;
use Narwhal\Card\Deck;

class Hand extends Deck
{
    protected $hand = [];

    public function __construct()
    {
        $this->hand = $this->deck->draw(5);
    }
}
