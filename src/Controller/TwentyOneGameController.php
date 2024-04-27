<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\Deck;
use App\Card\DeckJoker;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\VarDumper;

class TwentyOneGameController extends AbstractController
{

    #[Route("/game", name: "game")]
    public function home(): Response {
        $data = [
            "hello" => "Welcome to Game 21!",
        ];
        return $this->render('game/index.html.twig', $data);
    }

    #[Route("/game/doc", name: "doc")]
    public function docs(): Response {
        $data = [
            "hello" => "This is the documentation route",
        ];
        return $this->render('game/docs.html.twig', $data);
    }

    #[Route("/game/init", name: "init_game")]
    public function initCard(
        SessionInterface $session
    ): Response {

        //Initialize deck
        $deck = new Deck();
        // $deck = new DeckJoker();

        //Initialize user hand and bank hand

        //Set deck and hand in session
        $session->set("deck", $deck);
        $session->set("cards", []);
        $session->set("asdf", "");
        $session->set("bank", []);
        $session->set("player_score", 0);
        $session->set("bank_score", 0);
        $session->set("finished", false);

        $this->addFlash(
            'notice',
            'Deck has been initialized and stored in session.'
        );

        return $this->redirectToRoute('gameplan');
    }

    #[Route("/game/gameplan", name: "gameplan", methods: "GET")]
    public function GamePlan21GET(
        SessionInterface $session
    ): Response {

        // Get deck and drawn cards from session
        $deck = $session->get("deck");
        $cards = $session->get("cards");
        $bank = $session->get("bank");

        //Send card data to template
        $data = [
            "hello" => "Hello and welcome!",
            "card" => "asdf",
            "cards" => $cards,
            "asdf" => $session->get("asdf"),
            "slask" => $session->get("slask"),
            "bank" => $bank,
            "player_score" => $session->get("player_score"),
            "bank_score" => $session->get("bank_score"),
            "ace" => 0,
        ];

        return $this->render('game/game.html.twig', $data);
        
    }

    #[Route("/game/gameplan", name: "gameplan_post", methods: "POST")]
    public function GamePlan21POST(
        SessionInterface $session,
        Request $request
    ): Response {

        // Get deck and drawn cards from session
        $deck = $session->get("deck");
        $cards = $session->get("cards");

        // Fetch all form data
        $asdf = $request->request->all();
        $session->set("slask", "slask");
        // If draw then draw new card and render
        // If shuffle, init new game
        // If stop, game logic
        if (array_key_exists("draw", $asdf)) {
            $sum = $session->get("player_score");

            if ($sum < 21 && !$session->get("finished")) {
                $newCard = $deck->draw(); //Card object
                $newCardRank = $newCard[0]->getRank();
                // var_dump($new_card);
                $cards[] = $newCard[0]->getCard();
                if (str_contains("1JQK", $newCardRank)) {
                    $sum += 10;
                } elseif ($newCardRank === "A") {
                    $sum += 11;
                    $ace = $session->get("ace");
                    $session->set("ace", $ace + 1);
                } else {
                    $sum += intval($newCardRank);
                }
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
                    $this->addFlash(
                        'alert',
                        'You got fat and lost the game! Hit Shuffle to restart!'
                    );
                }
            }

            //Set new deck and drawn cards in session
            
        } elseif (array_key_exists("shuffle", $asdf)) {
            return $this->redirectToRoute('init_game');
        } elseif (array_key_exists("stop", $asdf)) {
            $session->set("finished", true);
            $bank = [];
            $session->set("ace", 0);
            if ($session->get("player_score") < 22 && $session->get("bank_score") == 0) {
                $sum = 0;
                while ($sum < 18 && $sum < $session->get("player_score")) {
                    $newCard = $deck->draw()[0];
                    $newCardRank = $newCard->getRank();
                    $bank[] = $newCard->getCard();
                    if (str_contains("1JQK", $newCardRank)) {
                        $sum += 10;
                    } elseif ($newCardRank === "A") {
                        $sum += 11;
                        $ace = $session->get("ace");
                        $session->set("ace", $ace + 1);
                    } else {
                        $sum += intval($newCardRank);
                    }
                    if ($sum > 17) {
                        $ace = $session->get("ace");
                        if ($ace > 0) {
                            $sum -= 10;
                            $session->set("ace", $ace - 1);
                        }
                    }
                    // $session->set("slask", $sum);
                }
                $session->set("bank", $bank);
                $session->set("bank_score", $sum);
            
                $sum = $session->get("bank_score");
                if ($sum < 22 && $sum >= $session->get("player_score")) {
                    $this->addFlash(
                        'alert',
                        'The bank won, you lost! Hit Shuffle to restart.'
                    );
                } else {
                    $this->addFlash(
                        'notice',
                        'You won the game! Hit Shuffle to restart.'
                    );
                }
            }
        }
            //Set new session data
            $session->set("deck", $deck);
            $session->set("cards", $cards);
            $session->set("asdf", $asdf);

        return $this->redirectToRoute('gameplan');
    }
}
