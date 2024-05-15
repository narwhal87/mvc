<?php

namespace App\Card;

use App\Card\Card;

class DeckJoker extends Deck
{
    public function __construct()
    {
        parent::__construct();

        $numSuits = sizeof(Card::$suits);

        for ($j = 0; $j < $numSuits; $j++) {
            $card = new Card(Card::$suits[$j], '🃟');
            array_push($this->deck, $card);
        }
    }
}
