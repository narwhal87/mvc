<?php

// Using (defining?) a namespace

namespace App\Controller;

// Using Symfony base classes
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Twig template handling in Abstract Controller base class
class LuckyControllerTwig extends AbstractController
{
    // Defining a route using Symfony base class Route
    #[Route("/lucky", name: "lucky_number")]

    // Creating a response function using Symfony base class Response
    public function number(): Response
    {
        $number = random_int(0, 100);

        $data = [
            'number' => $number
        ];

        return $this->render('lucky_number.html.twig', $data);
    }

    // Create a new route with Route symfony base class
    #[Route("/", name: "home")]
    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    #[Route("/about", name: "about")]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }

    #[Route("/report", name: "report")]
    public function report(): Response
    {
        return $this->render('report.html.twig');
    }
}
