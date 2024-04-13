<?php

namespace App\Controller;
use Narwhal\Card\Card;
use Narwhal\Card\Deck;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\VarDumper;

class CardGameController extends AbstractController
{
    // #[Route("/game/pig", name: "pig_start")]
    // public function home(): Response
    // {
    //     return $this->render('pig/home.html.twig');
    // }

    #[Route("/session", name: "session")]
    public function homeSession(
        SessionInterface $session
    ): Response
    {
        
        $data = [
            'session' => $session->all()
        ];

        return $this->render('session/index.html.twig', $data);
    }

    #[Route("/session/delete", name: "clear_session")]
    public function clearSession(
        SessionInterface $session
    ): Response
    {
        
        $session->clear();
        $this->addFlash(
            'notice',
            'Session has been cleared'
        );

        return $this->redirectToRoute('session');
    }

    #[Route("/card", name: "card")]
    public function home(
        // SessionInterface $session
    ): Response
    {
        
        $data = [
            'hello' => "This seems to work"
        ];

        return $this->render('card/index.html.twig', $data);
    }

    #[Route("/card/init", name: "init_card")]
    public function initCard(
        SessionInterface $session
    ): Response
    {
        $deck = new Deck();
        $session->set("deck", $deck);
        $this->addFlash(
            'notice',
            'Deck has been initialized and stored in session.'
        );

        return $this->redirectToRoute('show_all_cards');
    }

    #[Route("/card/test/draw", name: "test_draw_card")]
    public function testDraw(): Response
    {
        $card = new Card();
        $card2 = new Card('♠', 'J');
        $deck = new Deck();
        $deck->draw(10);

        $data = [
            "card" => $card->draw(),
            "card2" => $card2->getCard(),
            "size" => $deck->getSizeOfDeck()
        ];

        return $this->render('card/test/test_draw.html.twig', $data);
    }

    #[Route("/card/test/deck", name: "test_deck")]
    public function testDeck(): Response
    {
        $deck = new Deck();
        $deck->flipRanksSuits();
        $cardsArr = $deck->getAsString();
        $cards = $deck->draw(40);

        $data = [
            "deck" => $deck->getAsString()
        ];

        return $this->render('card/test/test_deck.html.twig', $data);
    }

    #[Route("/card/deck", name: "show_all_cards")]
    public function showAllCardsInDeck(
        SessionInterface $session
    ): Response
    {
        // Make sure session variable "deck" is set
        $deck = $session->get("deck");
        $data = [
            "deck" => $deck->getAsString()
        ];

        return $this->render('card/deck.html.twig', $data);
    }

    #[Route("/card/deck/shuffle", name: "shuffle_deck")]
    public function shuffleDeck(
        SessionInterface $session
    ): Response
    {
        $deck = new Deck();
        $deck->shuffleDeck();
        $session->set("deck", $deck);

        $data = [
            "deck" => $deck->getAsString()
        ];

        return $this->render('card/deck.html.twig', $data);
    }

    #[Route("/card/deck/draw", name: "draw_card")]
    public function drawCardFromDeck(
        SessionInterface $session
    ): Response
    {
        // If num is larger than or equal to deck size, flash message and redirect to end route

        $deck = $session->get("deck");

        $data = [
            "cards" => $deck->draw(),
            "size" => $deck->getSizeOfDeck()
        ];

        $session->set("deck", $deck);

        return $this->render('card/draw.html.twig', $data);
    }

    #[Route("/card/deck/draw/{num<\d+>}", name: "draw_many_cards")]
    public function drawManyCards(
        int $num,
        SessionInterface $session
    ): Response
    {
        // If num is larger than or equal to deck size, flash message and redirect to end route
        
        $deck = $session->get("deck");

        $data = [
            "cards" => $deck->draw($num),
            "size" => $deck->getSizeOfDeck()
        ];

        $session->set("deck", $deck);

        return $this->render('card/draw.html.twig', $data);
    }

}
