<?php

namespace App\Card;

use PHPUnit\Framework\TestCase;
use App\Card\Deck;

/**
 * Test class for class Deck
 */
class DeckTest extends TestCase {

    private $deck;

    protected function setUp(): void
    {
        $this->deck = new Deck();
    }

    public function testCreateDeck() {
        $this->assertInstanceOf("\App\Card\Deck", $this->deck);
    }

    public function testGetSize() {
        $this->assertEquals($this->deck->getSizeOfDeck(), 52);
    }

    public function testDeckHasCardObjects() {
        $deckArray = $this->deck->getDeckArray();
        $this->assertIsArray($deckArray);
        $this->assertInstanceOf("\App\Card\Card", $deckArray[0]);
    }

    public function testDeckAsJSONReturnsArrayOfString() {
        $deckJSON = $this->deck->getDeckAsJSON();
        $this->assertIsArray($deckJSON);
        $this->assertIsString($deckJSON[0]);
    }

    public function testDeckDrawEmpty() {
        $cards = $this->deck->draw();
        $this->assertIsArray($cards);
        $this->assertCount(1, $cards);
        $this->assertInstanceOf("\App\Card\Card", array_pop($cards));
        $this->assertEquals($this->deck->getSizeOfDeck(), 51);
    }

    public function testDeckDrawMany() {
        $num = 5;
        $cards = $this->deck->draw($num);
        $this->assertIsArray($cards);
        $this->assertCount($num, $cards);
        $this->assertInstanceOf("\App\Card\Card", array_pop($cards));
        $this->assertEquals($this->deck->getSizeOfDeck(), 52 - $num);
    }

    public function testDeckShuffle() {
        $old = $this->deck->getDeckArray();
        $this->deck->shuffleDeck();
        $this->assertInstanceOf("\App\Card\Deck", $this->deck);
        $this->assertEquals($this->deck->getSizeOfDeck(), 52);
        $this->assertNotEquals($old, $this->deck->getDeckArray());
    }

    public function testSortDeck() {
        $old = $this->deck->getDeckArray();
        $this->deck->shuffleDeck();
        $this->assertNotEquals($old, $this->deck->getDeckArray());
        $this->deck->sortDeck();
        $this->assertEquals($old, $this->deck->getDeckArray());
    }
    
}