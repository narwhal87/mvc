<?php

namespace Narwhal\Card;
use Narwhal\Card\Card;

class DeckJoker extends Deck {

    public function __construct() {
        parent::__construct();
        for ($j = 0; $j < sizeof(Card::$suits); $j++) {
            $card = new Card(Card::$suits[$j], '🃟');
            array_push($this->deck, $card);
        }    
    }
}