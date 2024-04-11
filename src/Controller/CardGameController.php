<?php

namespace App\Controller;

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
}
