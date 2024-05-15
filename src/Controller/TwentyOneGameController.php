<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\Deck;
use App\Card\DeckJoker;
use App\Card\Game;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

// use Symfony\VarDumper;

class TwentyOneGameController extends AbstractController
{
    #[Route("/game", name: "game")]
    public function home(): Response
    {
        $data = [
            "hello" => "Welcome to Game 21!",
        ];
        return $this->render('game/index.html.twig', $data);
    }

    #[Route("/game/doc", name: "doc")]
    public function docs(): Response
    {
        $data = [
            "hello" => "This is the documentation route",
        ];
        return $this->render('game/docs.html.twig', $data);
    }

    #[Route("/game/init", name: "init_game")]
    public function initCard(
        SessionInterface $session
    ): Response {

        //Initialize game
        //Set deck and hand in session
        $game = new Game();
        $game->initGame($session);

        $this->addFlash(
            'notice',
            'Deck has been initialized and stored in session.'
        );

        return $this->redirectToRoute('gameplan');
    }

    #[Route("/game/gameplan", name: "gameplan", methods: "GET")]
    public function gamePlan21Get(
        SessionInterface $session
    ): Response {

        // Get deck and drawn cards from session
        //Send card data to template
        $data = [
            "hello" => "Hello and welcome!",
            "card" => "asdf",
            "cards" => $session->get("cards"),
            "asdf" => $session->get("asdf"),
            "slask" => $session->get("slask"),
            "bank" => $session->get("bank"),
            "player_score" => $session->get("player_score"),
            "bank_score" => $session->get("bank_score"),
            "ace" => 0,
        ];

        return $this->render('game/game.html.twig', $data);

    }

    #[Route("/game/gameplan", name: "gameplan_post", methods: "POST")]
    public function gamePlan21Post(
        SessionInterface $session,
        Request $request
    ): Response {

        $game = new Game();
        // Fetch all form data
        $asdf = $request->request->all();
        $session->set("slask", "slask");
        // If draw then draw new card and render
        // If shuffle, init new game
        // If stop, game logic
        if (array_key_exists("draw", $asdf)) {

            $game->playerDraw($session);
            $sum = $session->get("player_score");
            if ($sum > 21) {
                $session->set("finished", true);
                $this->addFlash(
                    'alert',
                    'You got fat and lost the game! Hit Shuffle to restart!'
                );
            }

            //Set new deck and drawn cards in session

        } elseif (array_key_exists("shuffle", $asdf)) {
            return $this->redirectToRoute('init_game');
        } elseif (array_key_exists("stop", $asdf)) {
            $game->bankDraw($session);

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
        //Set new session data
        $session->set("asdf", $asdf);

        return $this->redirectToRoute('gameplan');
    }
}
