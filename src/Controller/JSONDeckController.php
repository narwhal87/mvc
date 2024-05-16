<?php

namespace App\Controller;

use App\Card\Card;
use App\Card\Deck;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class JSONDeckController extends AbstractController
{
    #[Route("/api/deck", name: 'api_deck', methods: ['GET'])]
    public function jsonGetDeck(
        SessionInterface $session
    ): Response {
        //if isset
        $deck = $session->get("deck");
        $deck->sortDeck();

        $data = [
            'deck' => $deck->getDeckAsJSON()
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
        return $response;
    }

    #[Route("/api/deck/shuffle", name: "api_post_shuffle", methods: ['POST'])]
    public function jsonShufflePost(
        SessionInterface $session
    ): Response {
        //if isset
        $deck = new Deck();
        $deck->shuffleDeck();
        $session->set("deck", $deck);

        return $this->redirectToRoute('api_get_shuffle');
    }

    #[Route("/api/deck/shuffle", name: "api_get_shuffle", methods: ['GET'])]
    public function jsonShuffleGet(
        SessionInterface $session
    ): Response {
        //if isset
        $deck = $session->get("deck");

        $data = [
            'deck' => $deck->getDeckAsJSON()
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
        return $response;
    }

    #[Route("/api/deck/draw", name: "api_post_draw", methods: ['POST'])]
    public function jsonDrawPost(
        SessionInterface $session
    ): Response {
        //if isset
        $deck = $session->get("deck");
        $cards = $deck->draw();

        $session->set("deck", $deck);
        $session->set("cards", $cards);

        return $this->redirectToRoute('api_get_draw');
    }

    #[Route("/api/deck/draw", name: "api_get_draw", methods: ['GET'])]
    public function jsonDrawGet(
        SessionInterface $session
    ): Response {
        //if isset
        $cards = [];
        $deck = $session->get("deck");
        foreach ($session->get("cards") as $card) {
            $cards[] = $card->getCard();
        }
        $data = [
            'cards' => $cards,
            'size' => $deck->getSizeOfDeck()
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
        return $response;
    }

    #[Route("/api/deck/draw/drawmany", name: "api_post_draw_many", methods: ['POST'])]
    public function jsonDrawManyPost(
        Request $request,
        SessionInterface $session
    ): Response {
        //if isset
        $num = $request->get('num');
        $deck = $session->get("deck");

        if (empty($deck)) {
            $deck = new Deck();
            $session->set("deck", $deck);
        }

        $cards = $deck->draw($num);

        $session->set("deck", $deck);
        $session->set("cards", $cards);

        return $this->redirectToRoute('api_get_draw_many', ['num' => $num]);
    }

    #[Route("/api/deck/draw/{num<\d+>}", name: "api_get_draw_many", methods: ['GET'])]
    public function jsonDrawManyGet(
        int $num,
        SessionInterface $session
    ): Response {
        //if isset
        $deck = $session->get("deck");
        $sizeDeck = $deck->getSizeOfDeck();
        if ($num > $sizeDeck) {
            $response = new JsonResponse(['error' => 'Not enough cards left']);
            $response->setEncodingOptions(
                $response->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );
            return $response;
        }
        $cards = [];
        if (empty($session->get("cards"))) {
            for ($i = 0; $i < $num; $i++) {
                $card = $deck->draw()[0];
                $cards[] = $card->getCard();
            }
        } else {
            foreach ($session->get("cards") as $card) {
                $cards[] = $card->getCard();
            }
            $session->set("cards", null);
        }
        $data = [
            'cards' => $cards,
            'size' => $deck->getSizeOfDeck()
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
        return $response;
    }

    #[Route("/api/game", name: "api_game", methods: ['GET'])]
    public function jsonGame21(
        SessionInterface $session
    ): Response {
        //if isset
        $deck = $session->get("deck");

        $data = [
            'cards' => $session->get("cards"),
            'bank' => $session->get("bank"),
            'player_score' => $session->get("player_score"),
            'bank_score' => $session->get("bank_score"),
            'finished' => $session->get('finished'),
            'size' => null,
        ];
        if ($deck) {
            $data["size"] = $deck->getSizeOfDeck();
        }

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
        return $response;
    }
}
