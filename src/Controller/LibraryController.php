<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Book;

class LibraryController extends AbstractController
{
    #[Route('/library', name: 'app_library')]
    public function index(
        BookRepository $bookRepository
    ): Response
    {
        $books = $bookRepository->findAll();
        return $this->render('library/library.html.twig', ['books' => $books]);
    }

    #[Route('/library/show/{id}', name: 'product_by_id')]
    public function showBookById(
        BookRepository $bookRepository,
        int $id
    ): Response
    {
        $book = $bookRepository->find($id);
        return $this->render('library/show.html.twig', ['book' => $book]);
    }

    // Routes (get-formulär => post) för att lägga till en bok C
    #[Route('/library/create_form', name: "book_create_form")]
    public function createBookForm(): Response {
        return $this->render("library/create.html.twig");
    }

    #[Route('/library/book_create', name: "book_create", methods: ["POST"])]
    public function createBook(
        ManagerRegistry $doctrine,
        Request $request
    ): Response
    {
        $entityManager = $doctrine->getManager();

        $book = new Book();
        $book->setIsbn($request->get('isbn'));
        $book->setTitle($request->get('title'));
        $book->setFirstname($request->get('firstname'));
        $book->setSurname($request->get('surname'));
        $book->setImage($request->get('image'));

        $entityManager->persist($book);
        $entityManager->flush();
        return $this->redirect('/');
    }

    // Route (GET) för att visa en bok R

    // Route (GET) för att visa alla böcker R

    // Route (GET-formulär => post) för att ändra i en bok

    // Route (GET-formulär => post) för att ta bort en bok D

 
}
