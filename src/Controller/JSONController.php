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
use App\Entity\Library;
use App\Repository\LibraryRepository;

class JSONController extends AbstractController
{
    #[Route("/api", name: "api_index")]
    public function jsonAll(): Response
    {
        $data = [ // Route address, description, route name
            "/api/lucky/number" => ["/api/lucky/number", 'Offer the visitor a random number', 'lucky_number'],
            '/api/quote' => ['/api/quote', 'Deliver quote of the day', 'api_quote'],
            '/api/deck' => ['/api/deck', 'A deck of cards', 'api_deck'],
            '/api/deck/shuffle' => ['/api/deck/shuffle', 'A shuffeled of cards', 'api_get_shuffle'],
            '/api/game' => ['/api/game', '21 Game status', 'api_game'],
            '/api/library/books' => ['/api/library/books', 'library books', 'api_library_books'],
        ];

        $data = ["data" => $data];

        return $this->render('/api/index.html.twig', $data);
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

        $randKey = array_rand($quotes);
        $data = [
            'quote' => date('m/d/Y h:i:s a', time()) . ": " . $quotes[$randKey]
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/library/books", name: 'api_library_books', methods: ['GET'])]
    public function jsonLibraryBooks(
        LibraryRepository $libraryRepository,
    ): Response {
        //if isset
        $books = $libraryRepository->findAll();

        $response = $this->json($books);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/library/book/{isbn}", name: 'api_library_book', methods: ['GET'])]
    public function jsonLibraryBook(
        LibraryRepository $libraryRepository,
        int $isbn
    ): Response {
        //if isset
        $book = $libraryRepository
            ->getBookDetails($isbn);

        $response = $this->json($book);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
