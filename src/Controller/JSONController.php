<?php

namespace App\Controller;
use Narwhal\Card\Card;
use Narwhal\Card\Deck;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class JSONController extends AbstractController
{
    #[Route("/api", name: "api_index")]
    public function jsonAll(): Response
    {
        $data = [
            "/api/lucky/number" => ["/api/lucky/number", 'Offer the visitor a random number', 'lucky_number'],
            '/api/quote' => ['/api/quote', 'Deliver quote of the day', 'api_quote'],
            '/api/deck' => ['/api/deck', 'A deck of cards', 'api_deck'],
            '/api/deck/shuffle' => ['/api/deck/shuffle', 'A shuffeled of cards', 'api_get_shuffle'],
        ];
        
        $data = ["data" => $data];

        // $response = new JsonResponse($data);
        // $response->headers->set('Content-Type', 'application/json');
        // $response->setEncodingOptions(
        //     $response->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            // $response->getEncodingOptions() | JSON_PRETTY_PRINT

            // From MVC GIT:

            // $response->getEncodingOptions() ||
            // JSON_PRETTY_PRINT ||
            // JSON_UNESCAPED_UNICODE
        // );

        // $data2 = ["response" => $response];

        return $this->render('/api/index.html.twig', $data);

        // return $response;
    }
    
    #[Route("/api/lucky/number", name: 'lucky_number')]
    public function jsonNumber(): Response
    {
        $number = random_int(0, 100);

        $data = [
            'lucky-number' => $number,
            'lucky-message' => 'Hi there!',
        ];

        // return new JsonResponse($data);

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/quote", name: "api_quote")]
    public function jsonQuote(): Response
    {
        $quotes = array(
            "Att tala är silver, att tiga är guld",
            "Det som inte dödar, det härdar",
            "Den som lever får se",
            "You miss 100% of all the shots you do not take - Wayne Gretzky - Michael Scott"
        );

        $rand_key = array_rand($quotes);
        $data = [
            'quote' => date('m/d/Y h:i:s a', time()) . ": " . $quotes[$rand_key]
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            JSON_UNESCAPED_UNICODE | $response->getEncodingOptions(JSON_UNESCAPED_UNICODE) | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck",  name: 'api_deck', methods: ['GET'])]
    public function jsonGetDeck(
        SessionInterface $session
    ): Response
    {
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

    #[Route("/api/deck/shuffle",  name: "api_post_shuffle", methods: ['POST'])]
    public function jsonShufflePost(
        SessionInterface $session
    ): Response
    {
        //if isset
        $deck = new Deck();
        $deck->shuffleDeck();
        $session->set("deck", $deck);

        return $this->redirectToRoute('api_get_shuffle');
    }

    #[Route("/api/deck/shuffle",  name: "api_get_shuffle", methods: ['GET'])]
    public function jsonShuffleGet(
        SessionInterface $session
    ): Response
    {
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

    #[Route("/api/deck/draw",  name: "api_post_draw", methods: ['POST'])]
    public function jsonDrawPost(
        SessionInterface $session
    ): Response
    {
        //if isset
        $deck = $session->get("deck");
        $cards = $deck->draw();

        $session->set("deck", $deck);
        $session->set("cards", $cards);

        return $this->redirectToRoute('api_get_draw');
    }

    #[Route("/api/deck/draw",  name: "api_get_draw", methods: ['GET'])]
    public function jsonDrawGet(
        SessionInterface $session
    ): Response
    {
        //if isset
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

    #[Route("/api/deck/draw/drawmany",  name: "api_post_draw_many", methods: ['POST'])]
    public function jsonDrawManyPost(
        Request $request,
        SessionInterface $session
    ): Response
    {
        //if isset
        $num = $request->request->get('num');
        $deck = $session->get("deck");
        $cards = $deck->draw($num);

        $session->set("deck", $deck);
        $session->set("cards", $cards);

        return $this->redirectToRoute('api_get_draw_many', ['num' => $num]);
    }

    #[Route("/api/deck/draw/{num<\d+>}",  name: "api_get_draw_many", methods: ['GET'])]
    public function jsonDrawManyGet(
        int $num,
        SessionInterface $session
    ): Response
    {
        //if isset
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
}
