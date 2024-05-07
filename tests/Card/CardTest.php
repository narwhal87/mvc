<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;

/**
 * Test class for class Card
 */
class CardTest extends TestCase {
    
    public function testCreateCardObjectEmpty() {
        $card = new Card();
        $this->assertInstanceOf("\App\Card\Card", $card);
        $this->assertNull($card->getSuit());
        $this->assertNull($card->getRank());
        $this->assertNull($card->getCard());
    }

    public function testCreateCardObjectFromValues() {
        $card = new Card('♠', 'J');
        $this->assertInstanceOf("\App\Card\Card", $card);
        $this->assertEquals($card->getSuit(), '♠');
        $this->assertEquals($card->getRank(), 'J');
        $this->assertEquals($card->getCard(), '♠J');
    }

    public function testCreateCardObjectFromValuesWrongType() {
        $rank = Card::$ranks[12];
        $suit = Card::$suits[0];
        $card = new Card($suit, $rank);
        $this->expectOutputString('♠A');
        $card->printCard();
    }

}
