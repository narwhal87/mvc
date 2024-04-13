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

        return $this->render('card/test/draw.html.twig', $data);
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

        return $this->render('card/test/deck.html.twig', $data);
    }

    #[Route("/card/deck", name: "show_all_cards")]
    public function showAllCardsInDeck(): Response
    {
        $deck = new Deck();
        $deck->shuffleDeck();

        $data = [
            "deck" => $deck->getAsString()
        ];

        return $this->render('card/deck.html.twig', $data);
    }


}
