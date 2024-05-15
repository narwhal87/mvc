<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use App\Card\DeckJoker;

/**
 * Test class for class Deck
 */
class DeckJokerTest extends TestCase {

    private $deck;

    protected function setUp(): void
    {
        $this->deck = new DeckJoker();
    }

    public function testCreateDeckJoker() {
        $this->assertInstanceOf("\App\Card\DeckJoker", $this->deck);
    }
    
}