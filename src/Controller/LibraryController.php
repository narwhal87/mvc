<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Library;
use App\Repository\LibraryRepository;
use Doctrine\Persistence\ManagerRegistry;

class LibraryController extends AbstractController
{
    #[Route('/library', name: 'library')]
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
        if (is_int($request->get("isbn"))) {
            $book->setIsbn($request->get("isbn"));
        }
        $book->setImg($request->get("img"));

        // tell Doctrine you want to (eventually) save the book
        // (no queries yet)
        $entityManager->persist($book);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        $this->addFlash(
            'notice',
            $title . " was created. :D"
        );

        // return new Response('Saved new book with id '.$book->getId());

        return $this->redirectToRoute('library_create_get');
    }

    #[Route('/library/show/{id}', name: 'book_by_id')]
    public function showBookById(
        LibraryRepository $libraryRepository,
        int $id
    ): Response {
        $book = $libraryRepository
            ->find($id);

        if (!$book) {
            throw $this->createNotFoundException(
                'No book found for id '.$id
            );
        }

        $data = [
            'books' => [$book]
        ];

        return $this->render('library/show.html.twig', $data);
    }

    #[Route('/library/view', name: 'library_view_all')]
    public function viewAllBooks(
        LibraryRepository $libraryRepository
    ): Response {
        $books = $libraryRepository->findAll();

        if (!$books) {
            throw $this->createNotFoundException(
                'The library is empty.'
            );
        }

        $data = [
            'books' => $books
        ];

        return $this->render('library/view.html.twig', $data);
    }

    #[Route('/library/update/{id}', name: 'library_update_get', methods: 'GET')]
    public function updateBookGet(
        LibraryRepository $libraryRepository,
        int $id
    ): Response {

        $book = $libraryRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException(
                'No book found for id '.$id
            );
        }

        $data = [
            'books' => [$book]
        ];
        return $this->render('library/update.html.twig', $data);
    }

    #[Route('/library/update/{id}', name: 'library_update_post', methods: 'POST')]
    public function updateBookPost(
        Request $request,
        LibraryRepository $libraryRepository,
        ManagerRegistry $doctrine,
        int $id
    ): Response {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Library::class)->find($id);
        $details = $libraryRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException(
                'No book found for id '.$id
            );
        }

        $book->setTitle($request->get('title'));
        $book->setIsbn($request->get('isbn'));
        $book->setAuthor($request->get('author'));
        $book->setImg($request->get('img'));

        $entityManager->flush();

        $this->addFlash(
            'notice',
            $details->getTitle() . " was updated. :)"
        );

        return $this->redirectToRoute('library_update_get', ['id' => $id]);
    }

    #[Route('/library/delete/{id}', name: 'library_delete')]
    public function deleteBookPost(
        LibraryRepository $libraryRepository,
        ManagerRegistry $doctrine,
        int $id
    ): Response {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Library::class)->find($id);
        $details = $libraryRepository->find($id);

        if (!$book || !$details) {
            throw $this->createNotFoundException(
                'No book found for id '.$id
            );
        }

        $entityManager->remove($book);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            $details->getTitle() . " was removed. :("
        );

        return $this->redirectToRoute('library_view_all');
    }
}
