<?php

namespace Narwhal\Card;

use Narwhal\Card\Card;

class Deck
{
    protected $deck = [];

    public function __construct() {
        // var_dump(Card::$suits);
        for ($i = 0; $i < sizeof(Card::$suits); $i++) {
            for ($j = 0; $j < sizeof(Card::$ranks); $j++) {
                $card = new Card(Card::$suits[$i], Card::$ranks[$j]);
                array_push($this->deck, $card);
            }    
        }
        // var_dump($this->deck);
    }

    public function flipRanksSuits() {
        $this->deck = [];
        for ($i = 0; $i < sizeof(Card::$ranks); $i++) {
            for ($j = 0; $j < sizeof(Card::$suits); $j++) {
                $card = new Card(Card::$suits[$j], Card::$ranks[$i]);
                array_push($this->deck, $card);
            }    
        }
    }

    public function getAsString(): array {
        $return_arr = [];
        foreach ($this->deck as &$card) {
            array_push($return_arr, $card->getCard());
        }
        // var_dump($return_arr);
        return $return_arr;
    }

    public function draw(int $num = 1): array {
        $cards = [];
        if ($num === 1) {
            $randKey[] = array_rand($this->deck, $num);
        } else {
            $randKey = array_rand($this->deck, $num);
        }
        rsort($randKey);
        var_dump($randKey);
        for ($i = 0; $i < sizeof($randKey); $i++) {
            $cards[] = $this->deck[$randKey[$i]];
            array_splice($this->deck, $randKey[$i], 1);
            // var_dump($this->deck);
        }
        return $cards;
    }

    public function shuffleDeck() {
        shuffle($this->deck);
    }

    public function getSizeOfDeck(): int {
        return sizeof($this->deck);
    }

    // public function add(Dice $die): void
    // {
    //     $this->hand[] = $die;
    // }

    // public function roll(): void
    // {
    //     foreach ($this->hand as $die) {
    //         $die->roll();
    //     }
    // }

    // public function getNumberDices(): int
    // {
    //     return count($this->hand);
    // }

    // public function getValues(): array
    // {
    //     $values = [];
    //     foreach ($this->hand as $die) {
    //         $values[] = $die->getValue();
    //     }
    //     return $values;
    // }

    // public function getString(): array
    // {
    //     $values = [];
    //     foreach ($this->hand as $die) {
    //         $values[] = $die->getAsString();
    //     }
    //     return $values;
    // }
}
