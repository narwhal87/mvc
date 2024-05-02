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
    public ?Deck $deck;

    public function __construct()
    {
        $this->playerScore = 0;
        $this->bankScore = 0;
    }

    public function initGame($session)
    {
        $this->deck = new Deck();
        $session->set("deck", $this->deck);
        $session->set("cards", []);
        $session->set("asdf", "");
        $session->set("bank", []);
        $session->set("player_score", 0);
        $session->set("bank_score", 0);
        $session->set("finished", false);
    }

    public function updateSum($newCardRank, $session) {
        if (str_contains("1JQK", $newCardRank)) {
            return 10;
        } elseif ($newCardRank === "A") {
            $ace = $session->get("ace");
            $session->set("ace", $ace + 1);
            return 11;
        } else {
            return intval($newCardRank);
        }
    }

    public function playerDraw($session)
    {
        $sum = $session->get("player_score");
        $deck = $session->get("deck");
        $cards = $session->get("cards"); //Array with player cards as str "<suit><rank>"

        //If not fat
        if ($sum < 21 && !$session->get("finished")) {
            $newCard = $deck->draw(); //Card object
            $newCardRank = $newCard[0]->getRank();
            // var_dump($new_card);
            $cards[] = $newCard[0]->getCard();
            $sum += $this->updateSum($newCardRank, $session);
            $session->set("player_score", $sum);
        }

        // If fat
        if ($sum > 21) {
            $ace = $session->get("ace");
            if ($ace > 0) {
                $session->set("player_score", $sum - 10);
                $session->set("ace", $ace - 1);
            }
        }

        $session->set("deck", $deck);
        $session->set("cards", $cards);
    }

    public function bankDraw($session)
    {
        $deck = $session->get("deck");
        $session->set("finished", true);
        $bank = [];
        $session->set("ace", 0);
        if ($session->get("player_score") < 22 && $session->get("bank_score") == 0) {
            $sum = 0;
            while ($sum < 18 && $sum < $session->get("player_score")) {
                $newCard = $deck->draw()[0];
                $newCardRank = $newCard->getRank();
                $bank[] = $newCard->getCard();
                $sum += $this->updateSum($newCardRank, $session);
                if ($sum > 17) {
                    $ace = $session->get("ace");
                    if ($ace > 0) {
                        $sum -= 10;
                        $session->set("ace", $ace - 1);
                    }
                }
            }
            $session->set("deck", $deck);
            $session->set("bank", $bank);
            $session->set("bank_score", $sum);
        }
    }
}
