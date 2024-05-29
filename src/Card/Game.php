<?php

namespace App\Card;

use App\Card\Card;
use App\Card\Deck;

// namespace Narwhal\Dice\Dice;

/**
 * This class contains the 21 Game logic
 * This is also a DocBlock comment
 */
class Game
{
    protected $player = []; // Holds Card objects
    protected ?int $playerScore;
    protected $bank = []; // Holds Card objects
    protected ?int $bankScore;
    public ?Deck $deck;

    /**
     * Constructor of Game class.
     */
    public function __construct()
    {
        $this->playerScore = 0;
        $this->bankScore = 0;
    }

    /**
     * Returns score of player
     * 
     */
    public function getPlayerScore()
    {
        return $this->playerScore;
    }

    /**
     * Returns score of bank
     * 
     */
    public function getBankScore()
    {
        return $this->bankScore;
    }

    /**
     * Initializes new deck and necessary session variables
     * 
     * @param object $session Symfony session variable
     */
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

    /**
     * Takes a Card object and returns the value addet to a sum
     * 
     * @param string $newCardRank rank of card (1 is 10)
     * @param object $session Symfony session variable
     */
    public function updateSum($newCardRank, $session)
    {
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

    /**
     * Player draw a new card from deck and store deck in session
     */
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
            } else {
                $session->set("finished", true);
            }
        }

        $session->set("deck", $deck);
        $session->set("cards", $cards);
    }

    /**
     * Bank draw card turn until game is over
     */
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
                    $sum += $this->decreaseAce($session);
                }
            }
            $session->set("deck", $deck);
            $session->set("bank", $bank);
            $session->set("bank_score", $sum);
        }
    }

    /**
     * Method decreaseAce subtracts number of aces from session game variable 
     * 
     * @param object $session Symfony session variable
     */
    public function decreaseAce($session) {
        $ace = $session->get("ace");
        if ($ace > 0) {
            $session->set("ace", $ace - 1);
            return -10;
        }
        return 0;
    }
}
