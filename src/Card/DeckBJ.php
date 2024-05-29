<?php

namespace App\Card;

use App\Card\Card;
use Exception;

/**
 * Blackjack specific deck with four equal decks to draw cards from.
 */
class DeckBJ extends Deck
{
    protected $deck = [];

    /**
     * Constructor creates a deck of Card objects
     */
    public function __construct()
    {
        for ($i = 0; $i < 4; $i++) {
            parent::__construct();
        }
    }
}
