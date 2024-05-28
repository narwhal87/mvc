<?php

namespace App\Card;

use App\Card\Card;
use App\Card\Deck;
use App\Card\DeckBJ;
// use App\Card\Game;

// namespace Narwhal\Dice\Dice;

/**
 * This class contains the 21 Game logic
 * This is also a DocBlock comment
 */
class GameBJ extends Game
{
    protected $player = []; // Holds Card objects
    protected ?int $playerScore;
    protected $bank = []; // Holds Card objects
    protected ?int $bankScore;
    // public ?DeckBJ $deck;

    /**
     * Constructor of Game class.
     */
    public function __construct()
    {
        parent::__construct();
        // $this->playerScore = 0;
        // $this->bankScore = 0;
    }

    public function resetGame($session) {
        $session->set("cards", []);   // Cards drawn to the current (active) hand
        $session->set("asdf", "");   // 
        $session->set("bank", []);   // Cards drawn by the dealer (bank)
        $session->set("player_score", 0);   // Current (active) hand score
        $session->set("bank_score", 0);   // Dealer (bank) score
        $session->set("finished", false);   // True if the player is done (fat or stop)
        $session->set("hands", []);   // NEW all player hands, array of "cards" arrays
        $session->set("num_hands", 1);   // NEW number of hands
        $session->set("active", 0);   // NEW active hand as index 
        $session->set("score", []);
    }

    public function incrementActiveHand($session) {
        $active = $session->get("active");
        $session->set("active", $active += 1);
    }

    public function resetGameVariables($session) {
        $session->set("player_score", 0);
        $session->set("ace", 0);
    }

    // /**
    //  * Returns score of player
    //  */
    // public function getPlayerScore()
    // {
    //     return $this->playerScore;
    // }

    // /**
    //  * Returns score of bank
    //  */
    // public function getBankScore()
    // {
    //     return $this->bankScore;
    // }

    /**
     * Initializes new deck and necessary session variables
     * Overrides inherited Game class method
     */
    public function initGame($session, $numHands = 1)
    {
        $this->deck = new DeckBJ();
        $session->set("deck", $this->deck);   // Deck contains 4*52 cards
        $session->set("cards", []);   // Cards drawn to the current (active) hand
        $session->set("asdf", "");   // 
        $session->set("bank", []);   // Cards drawn by the dealer (bank)
        $session->set("player_score", 0);   // Current (active) hand score
        $session->set("bank_score", 0);   // Dealer (bank) score
        $session->set("finished", false);   // True if the player is done (fat or stop)
        $session->set("hands", []);   // NEW all player hands, array of "cards" arrays
        $session->set("num_hands", $numHands);   // NEW number of hands
        $session->set("active", 0);   // NEW active hand as index 
        $session->set("score", []);
    }

    // /**
    //  * Takes a Card object and returns the value addet to a sum
    //  */
    // public function updateSum($newCardRank, $session)
    // {
    //     if (str_contains("1JQK", $newCardRank)) {
    //         return 10;
    //     } elseif ($newCardRank === "A") {
    //         $ace = $session->get("ace");
    //         $session->set("ace", $ace + 1);
    //         return 11;
    //     } else {
    //         return intval($newCardRank);
    //     }
    // }

    /**
     * Player draw a new card from deck and store deck in session
     * Method overridden from Game class
     */
    public function playerDraw($session)
    {
        $active = $session->get("active"); // Get Active hand index
        if ($active >= $session->get("num_hands")) {
            $session->set("finished", true);
            $session->set("slask", $active . $session->get("num_hands"));
        }

        $sum = $session->get("player_score"); // Get Hand score
        $deck = $session->get("deck"); // Get Deck from session
        $cards = $session->get("cards"); // Array with player cards as str "<suit><rank>"
        $hands = $session->get("hands"); // Array with $cards[]
        $score = $session->get("score");

        //If not fat
        if ($sum < 21 && !$session->get("finished")) {
            $newCard = $deck->draw(); //Card object
            $session->set("deck", $deck);
            $newCardRank = $newCard[0]->getRank();
            // var_dump($new_card);
            $cards[] = $newCard[0]->getCard();
            $session->set("cards", $cards);
            if (array_key_exists($active, $hands)) { // If current hand has cards or not
                $hands[$active] = $cards;
                $session->set("slask", "hand index was found");
            } else {
                $hands[] = $cards;
                $session->set("slask", "hand index was not found");
            }
            
            $sum += $this->updateSum($newCardRank, $session);
            $session->set("player_score", $sum);
            if (array_key_exists($active, $score)) {
                $score[$active] = $sum;
            } else {
                $score[] = $sum;
            }
            $session->set("score", $score);
        }

        // If fat
        if ($sum > 21) {
            $ace = $session->get("ace");
            if ($ace > 0) {
                $session->set("player_score", $sum - 10);
                if (array_key_exists($active, $score)) {
                    $score[$active] = $sum - 10;
                } else {
                    $score[] = $sum - 10;
                }
                $session->set("score", $score);
                $session->set("ace", $ace - 1);
            } else {
                // $session->set("finished", true);
                // Increment "active" one step
                // Reset game variables of active is not hand_num
                $session->set("slask", "Player got fat");
                $this->incrementActiveHand($session);
                $this->resetGameVariables($session);
                $cards = [];
                $session->set("cards", $cards);
            }
        }

        $session->set("hands", $hands);
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
            while ($sum < 18 && $sum < max($session->get("score"))) {
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
