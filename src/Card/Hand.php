<?php

namespace App\Card;

use App\Card\Card;
use App\Card\Deck;

class Hand extends Deck
{
    protected $hand = [];

    public function __construct()
    {
        parent::__construct();
        $this->hand = $this->draw(5);
    }
}
