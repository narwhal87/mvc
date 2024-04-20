<?php

namespace Narwhal\Card;

// namespace Narwhal\Dice\Dice;

class Card
{
    public static $suits = ['♣', '♠', '♥', '♦'];
    public static $ranks = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'A'];
    protected ?string $suit;
    protected ?string $rank;
    protected ?string $card;

    public function __construct($suit = null, $rank = null)
    {
        $this->suit = $suit;
        $this->rank = $rank;
        if ($suit === null or $rank === null) {
            $this->card = null;
        } else {
            $this->card = strval($this->suit) . strval($this->rank);
        }
    }

    public function draw(): string
    {
        // $this->suit = array_rand(['\0xE2 0x99 0xA3', '\0xE2 0x99 0xA0', '\0xE2 0x99 0xA6', '\0xE2 0x99 0xA5'], 1);
        // $this->rank = array_rand(['2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K', 'E'], 1);
        $randKey = array_rand(self::$suits, 1);
        $randKey2 = array_rand(self::$ranks, 1);
        $this->suit = self::$suits[$randKey];
        $this->rank = self::$ranks[$randKey2];
        $this->card = strval($this->suit) . strval($this->rank);
        return strval($this->suit) . strval($this->rank);
    }

    public function printCard()
    {
        echo $this->card;
    }

    public function getCard(): string
    {
        return $this->card;
    }

    public function createDeck()
    {
        $deck = [];
        for ($i = 0; $i < sizeof(self::$suits); $i++) {
            for ($j = 0; $j < sizeof(self::$ranks); $j++) {
                array_push($deck, strval(self::$suits[$i]) . strval(self::$ranks[$j]));
            }
        }
        return $deck;
    }

}
