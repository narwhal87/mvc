<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LuckyControllerJSON
{
    #[Route("/api/lucky/number")]
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

    #[Route("/api")]
    public function jsonAll(): Response
    {
        $data = [
            '/api/lucky/number' => '/api/lucky/number',
            '/api/quote' => '/api/quote'
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            JSON_UNESCAPED_UNICODE | $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/quote")]
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
}
