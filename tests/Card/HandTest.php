<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use App\Card\Hand;

/**
 * Test class for class Deck
 */
class HandTest extends TestCase {

    private $hand;
    
    protected function setUp(): void
    {
        $this->hand = new Hand();
    }

    public function testCreateHand() {
        $this->assertInstanceOf("\App\Card\Hand", $this->hand);
    }
    
}