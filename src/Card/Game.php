<?php

namespace App\Card;
use App\Card\Card;
use App\Card\Deck;
// namespace Narwhal\Dice\Dice;

class Game
{
    protected $player = []; // Holds Card objects
    protected ?int $player_score;
    protected $bank = []; // Holds Card objects
    protected ?int $bank_score;
    
    public function __construct() {
        $this->player_score = 0;
        $this->bank_score = 0;
    }

    public function addCardToPlayer($card) {
        $this->player[] = $card;
        $this->player_score = calcSum($player);
    }

    public function addCardToBank($card) {
        $this->bank[] = $card;
        $this->bank_score = calcSum($bank);
    }

    public function calcSum($cardArr) {
        $sum = 0;
        foreach ($cardArr as &$card) {
            $cardStr = $card->getCard();
            if (strcontains("1JQK", $cardStr[1])) {
                $sum += 10;
            } elseif ($cardStr[1] === "A") {
                $sum += 11;
            } else {
                $sum += intval($cardStr[1]);
            }
        }
        return $sum;
    }

    public function getCardsAsStr($cardArr) {
        // check if non-empty
        $str = "";
        foreach ($cardArr as &$card) {
            $str += $card->getCard();
        }
    }
}
