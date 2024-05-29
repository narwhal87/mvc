<?php

namespace App\Card;

use App\Card\Card;
use App\Card\Deck;
use App\Card\DeckBJ;

/**
 * Blackjack game logic
 * 
 * @author Alf Tore Pettersson (alpt22@student.bth.se)
 */
class GameBJ extends Game
{
    protected $player = []; // Holds Card objects
    protected ?int $playerScore;
    protected $bank = []; // Holds Card objects
    protected ?int $bankScore;

    /**
     * Constructor of Game class.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * incrementActiveHand increments which hand is active for the player.
     * 
     * @param object $session Symfony session variable
     */
    public function incrementActiveHand($session): void
    {
        $active = $session->get("active");
        $session->set("active", $active += 1);
    }

    /**
     * resetGameVariables between each playable hand transition
     * 
     * @param object $session Symfony session variable
     */
    public function resetGameVariables($session): void
    {
        $session->set("player_score", 0);
        $session->set("ace", 0);
    }

    /**
     * @description Initializes new deck and necessary session variables
     * Overrides inherited Game class method
     * 
     * @param object $session Symfony session variable
     * @param int $numHands number of playable hands chosen by player
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

    /**
     * Player draw a new card from deck and store deck in session
     * Method overridden from Game class
     * 
     * @param object $session Symfony session variable
     */
    public function playerDraw($session)
    {
        $active = $session->get("active"); // Get Active hand index
        if ($active >= $session->get("num_hands")) {
            $session->set("finished", true);
        }

        $sum = $session->get("player_score"); // Get Hand score
        $deck = $session->get("deck"); // Get Deck from session
        $cards = $session->get("cards"); // Array with player cards as str "<suit><rank>"
        $hands = $session->get("hands"); // Array with $cards[]
        $score = $session->get("score"); // All Hands score as array

        //If not fat
        if ($sum < 22 && !$session->get("finished")) {
            //Draw a card
            $newCard = $deck->draw(); //Card object
            $session->set("deck", $deck);
            $newCardRank = $newCard[0]->getRank();
            $cards[] = $newCard[0]->getCard();
            $session->set("cards", $cards);

            // Save card to hand
            if (array_key_exists($active, $hands)) { // If current hand has cards or not
                $hands[$active] = $cards;
            } else {
                $hands[] = $cards;
            }

            // Update sum of current hand
            $sum += $this->updateSum($newCardRank, $session);
            $session->set("player_score", $sum);

            // Save current hand score to all hands score array
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
     * Method from Game overriden
     * 
     * @param object $session Symfony session variable
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
                    $sum += $this->decreaseAce($session);
                }
            }
            $session->set("deck", $deck);
            $session->set("bank", $bank);
            $session->set("bank_score", $sum);
        }
    }
}
