<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Library;
use Doctrine\Persistence\ManagerRegistry;

class LibraryController extends AbstractController
{
    #[Route('/library', name: 'app_library')]
    public function index(): Response
    {
        return $this->render('library/index.html.twig', [
            'controller_name' => 'LibraryController',
        ]);
    }

    #[Route('/library/create', name: 'library_create_get', methods: 'GET')]
    public function createBookGet(
    ): Response {
        

        return $this->render('library/create.html.twig');
    }

    #[Route('/library/create', name: 'library_create_post', methods: 'POST')]
    public function createBookPost(
        ManagerRegistry $doctrine,
        Request $request,
    ): Response {
        $entityManager = $doctrine->getManager();
        $title = $request->get("title");
        $book = new Library();
        $book->setTitle($request->get("title"));
        $book->setAuthor($request->get("author"));
        $book->setIsbn($request->get("isbn"));

        // tell Doctrine you want to (eventually) save the book
        // (no queries yet)
        $entityManager->persist($book);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        // return new Response('Saved new book with id '.$book->getId());

        return $this->redirectToRoute('library_create_get');
    }

    
}
