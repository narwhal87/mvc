<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\Deck;
use App\Card\DeckJoker;
use App\Card\DeckBJ;
use App\Card\GameBJ;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

// use Symfony\VarDumper;

class ProjController extends AbstractController
{
    #[Route("/proj", name: "proj_home")]
    public function home(): Response
    {
        $data = [
            "hello" => "Welcome to Project Blackjack!",
        ];
        return $this->render('proj/index.html.twig', $data);
    }

    #[Route("/proj/about", name: "proj_about")]
    public function projAbout(): Response
    {
        return $this->render('proj/about.html.twig');
    }

    #[Route("/proj/docs", name: "proj_docs")]
    public function projDocs(): Response
    {
        return $this->redirectToRoute('metrics');
    }

    #[Route("/proj/init", name: "init_blackjack_post", methods: "POST")]
    public function initBlackJackPOST(
        SessionInterface $session,
        Request $request
    ): Response {
        $game = new GameBJ();
        $numHands = $request->get('num_hands');
        $game->initGame($session, intval($numHands));
        
        $this->addFlash(
            'notice',
            'Deck has been initialized and stored in session.'
        );
        $data = [
            "cards" => $session->get("cards"),
            "asdf" => $session->get("asdf"),
            "slask" => $session->get("slask"),
            "bank" => $session->get("bank"),
            "player_score" => $session->get("player_score"),
            "bank_score" => $session->get("bank_score"),
            "ace" => 0,
            "hands" => $session->get("hands"),
            "num_hands" => $session->get("num_hands"),
            "deck" => $session->get("deck")->getDeckAsJSON(),
        ];

        return $this->render('proj/game.html.twig', $data);
    }

    #[Route("/proj/init", name: "init_blackjack_get", methods: "GET")]
    public function initBlackJackGET(): Response
    {

        return $this->render('proj/init.html.twig');
    }

    #[Route("/proj/gameplan", name: "gameplan_blackjack_get", methods: "GET")]
    public function gamePlanBlackJackGet(
        SessionInterface $session
    ): Response {
        $data = [
            // "hello" => "Hello and welcome!",
            // "card" => "asdf",
            "cards" => $session->get("cards"),
            "asdf" => $session->get("asdf"),
            "slask" => $session->get("slask"),
            "bank" => $session->get("bank"),
            "player_score" => $session->get("player_score"),
            "bank_score" => $session->get("bank_score"),
            "ace" => 0,
            "hands" => $session->get("hands"),
            "num_hands" => $session->get("num_hands"),
            "deck" => $session->get("deck")->getDeckAsJSON(),
            "score" => $session->get("score"),
        ];
        // var_dump($data);

        return $this->render('proj/game.html.twig', $data);

    }

    #[Route("/proj/gameplan", name: "gameplan_blackjack_post", methods: "POST")]
    public function gamePlanBJPost(
        SessionInterface $session,
        Request $request
    ): Response {

        $game = new GameBJ();
        $asdf = $request->request->all();
        // $session->set("slask", "slask");

        if (array_key_exists("draw", $asdf) && !$session->get("finished")) {
            $game->playerDraw($session);
            $sum = $session->get("player_score");

            if ($sum == 21) {
                $this->addFlash(
                    'notice',
                    'You got Blackjack! You are advised to click on Stop...'
                );
                // $session->set("finished", true);
            }

            if ($sum > 21) {
                if ($session->get("active") == $session->get("num_hands") - 1) {
                    $session->set("finished", true);
                    $this->addFlash(
                        'alert',
                        'You got fat and lost the game!'
                    );
                } else {
                    $session->set("active", $session->get("active") + 1);
                    $session->set("finished", false);
                    $this->addFlash(
                        'alert',
                        'You got fat, but you still have hands available. Draw more cards.'
                    );
                }
            }

            if ($sum == 0) { // if draw, $sum is non-zero
                if ($session->get("active") == $session->get("num_hands")) {
                    $this->addFlash(
                        'alert',
                        'You got fat and lost the game!'
                    );
                } else {
                    $this->addFlash(
                        'alert',
                        'You got fat, but you still have hands available. Draw more cards.'
                    );
                }
            }
        } elseif (array_key_exists("shuffle", $asdf)) {
            return $this->redirectToRoute('init_blackjack_get');
        } elseif (array_key_exists("stop", $asdf) && !$session->get("finished") && $session->get("player_score") != 0) {

            // This happens if the player stops a hand
            if ($session->get("active") < $session->get("num_hands") - 1) {
                $game->incrementActiveHand($session);
                $game->resetGameVariables($session);
                $session->set("cards", []);

                $this->addFlash(
                    'notice',
                    'You stopped. Continue to draw your next hand'
                );
                return $this->redirectToRoute('gameplan_blackjack_get');
            }

            // This happens if the player stops on last hand
            $game->bankDraw($session);
            $session->set("finished", true);
            $sum = $session->get("bank_score");
            $dum = $session->get("score");
            $handsScore = [];
            foreach ($dum as &$value) {
                if ($value < 22) {
                    $handsScore[] = $value;
                }
            }

            if ($sum < 22 && $sum >= max($handsScore)) {
                $this->addFlash(
                    'alert',
                    'The bank won, you lost! Press Restart'
                );
            } else {
                $this->addFlash(
                    'notice',
                    'You won the game! Press Restart.'
                );
            }
        } elseif (array_key_exists("stop", $asdf) && $session->get("player_score") == 0) {
            $text = "";
            if ($session->get("active") == 0) {
                $text = 'You have to draw at least one card before you stop.';
            } elseif ($session->get("active") < $session->get("num_hands") - 1) {
                $text = 'You still have available hands. Draw a card!';
            } else {
                $text = 'The game is already finished. Press Restart to play again.';
            }
            $this->addFlash(
                'alert',
                $text
            );
        }
        //Set new session data
        $session->set("asdf", $asdf);

        return $this->redirectToRoute('gameplan_blackjack_get');
    }
}
