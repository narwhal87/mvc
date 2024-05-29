<?php

namespace App\Card;

use App\Card\Card;
use Exception;

class Deck
{
    protected $deck = [];

    /**
     * Constructor creates a deck of Card objects
     */
    public function __construct()
    {
        $numSuits = sizeof(Card::$suits);
        $numRanks = sizeof(Card::$ranks);

        for ($i = 0; $i < $numSuits; $i++) {
            for ($j = 0; $j < $numRanks; $j++) {
                $card = new Card(Card::$suits[$i], Card::$ranks[$j]);
                array_push($this->deck, $card);
            }
        }
    }

    public function getDeckAsJSON(): array
    {
        $returnArr = [];
        foreach ($this->deck as &$card) {
            array_push($returnArr, $card->getCard());
        }
        return $returnArr;
    }

    public function draw(int $num = 1): array
    {
        // Check if deck is large enough
        $randKey = array();
        $cards = [];
        if ($num === 1) {
            $randKey[] = array_rand($this->deck, $num);
        } else {
            $randKey = array_rand($this->deck, $num);
        }
        if (is_array($randKey)) {
            rsort($randKey);
        }
        // rsort($randKey);
        $sizeRandKey = sizeof($randKey);
        for ($i = 0; $i < $sizeRandKey; $i++) {
            $cards[] = $this->deck[$randKey[$i]];
            array_splice($this->deck, $randKey[$i], 1);
        }
        return $cards;
    }

    public function shuffleDeck()
    {
        shuffle($this->deck);
    }

    public function getSizeOfDeck(): int
    {
        return sizeof($this->deck);
    }

    public function getDeckArray(): array
    {
        return $this->deck;
    }

    /**
     * Sorts deck on suit then rank in ascending order
     */
    public function sortDeck()
    {
        sort($this->deck);
        $sizeDeck = sizeof($this->deck);
        for ($i = 0; $i < $sizeDeck - 1; $i++) {

            $card1 = $this->deck[$i]->getCard();
            $card2 = $this->deck[$i + 1]->getCard();
            if (substr($card1, -1) === "A") {
                if (str_contains("JQK", substr($card2, -1))) {
                    $temp = $this->deck[$i];
                    $this->deck[$i] = $this->deck[$i + 1];
                    $this->deck[$i + 1] = $temp;
                    $i -= 2;
                }
            } elseif (substr($card1, -1) === "K") {
                if (str_contains("Q", substr($card2, -1))) {
                    $temp = $this->deck[$i];
                    $this->deck[$i] = $this->deck[$i + 1];
                    $this->deck[$i + 1] = $temp;
                }
            }

        }
    }

}
