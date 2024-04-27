<?php

namespace App\Card;

use App\Card\Card;
use App\Card\Deck;

// namespace Narwhal\Dice\Dice;

class Game
{
    protected $player = []; // Holds Card objects
    protected ?int $playerScore;
    protected $bank = []; // Holds Card objects
    protected ?int $bankScore;

    public function __construct()
    {
        $this->playerScore = 0;
        $this->bankScore = 0;
    }

    // public function addCardToPlayer($card)
    // {
    //     $this->player[] = $card;
    //     $this->playerScore = calcSum($player);
    // }

    // public function addCardToBank($card)
    // {
    //     $this->bank[] = $card;
    //     $this->bankScore = calcSum($bank);
    // }

    public function calcSum($cardArr)
    {
        $sum = 0;
        foreach ($cardArr as &$card) {
            $cardStr = $card->getCard();
            if (str_contains("1JQK", $cardStr[1])) {
                $sum += 10;
            } elseif ($cardStr[1] === "A") {
                $sum += 11;
            } else {
                $sum += intval($cardStr[1]);
            }
        }
        return $sum;
    }

    public function getCardsAsStr($cardArr)
    {
        // check if non-empty
        $str = "";
        foreach ($cardArr as &$card) {
            $str += $card->getCard();
            return $str;
        }
    }
}
