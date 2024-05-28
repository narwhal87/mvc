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

    // #[Route("/game/doc", name: "doc")]
    // public function docs(): Response
    // {
    //     $data = [
    //         "hello" => "This is the documentation route",
    //     ];
    //     return $this->render('game/docs.html.twig', $data);
    // }

    #[Route("/proj/init", name: "init_blackjack_post", methods: "POST")]
    public function initBlackJackPOST(
        SessionInterface $session,
        Request $request
    ): Response {
        $game = new GameBJ();
        $numHands = $request->get('num_hands');
        $game->initGame($session, intval($numHands));
        var_dump($numHands);
        // echo "<script>console.log('Debug Objects: " . $numHands . "' );</script>";"deck" => $deck->getDeckAsJSON()
        $this->addFlash(
            'notice',
            'Deck has been initialized and stored in session.'
        );
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
        ];

        return $this->render('proj/game.html.twig', $data);
    }

    #[Route("/proj/init", name: "init_blackjack_get", methods: "GET")]
    public function initBlackJackGET(): Response {

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

            if ($sum > 21) {
                // $session->set("finished", true);
                $this->addFlash(
                    'alert',
                    'You got fat and lost the game! Hit Shuffle to restart!'
                );
            }
        } elseif (array_key_exists("shuffle", $asdf)) {
            return $this->redirectToRoute('init_blackjack_get');
        } elseif (array_key_exists("stop", $asdf) && !$session->get("finished") && $session->get("player_score") != 0) {

            // if active hand = num_hands then bank draw
            // if active hand < num_hands then continue

            // This happens if the player stops a hand
            if ($session->get("active") < $session->get("num_hands") - 1) {
                $session->set("slask", "player stopped");

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
            $session->set("slask", "bank draw");
            $game->bankDraw($session);
            $session->set("finished", true);
            $sum = $session->get("bank_score");
            if ($sum < 22 && $sum >= max($session->get("score"))) {
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
        //Set new session data
        $session->set("asdf", $asdf);

        return $this->redirectToRoute('gameplan_blackjack_get');
    }
}
