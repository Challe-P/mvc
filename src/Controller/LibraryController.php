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

    #[Route('/library/show/{id}', name: 'book_by_id')]
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
        Request $request,
        ManagerRegistry $doctrine
    ): Response
    {
        $book = new Book();
        $this->bookSetter($book, $request, $doctrine);
        return $this->redirectToRoute('app_library');
    }

    #[Route('/library/update_form/{id}', name: "book_update_form")]
    public function updateBookForm(
        BookRepository $bookRepository,
        int $id
    ): Response {
        $book = $bookRepository->find($id);
        return $this->render('library/update.html.twig', ["book" => $book]);
    }

    #[Route('library/book_update', name: "book_update", methods: ["POST"])]
    public function updateBook(
        Request $request,
        BookRepository $bookRepository,
        ManagerRegistry $doctrine
    ): Response
    {
        $book = $bookRepository->find($request->get("id"));
        $this->bookSetter($book, $request, $doctrine);
        return $this->redirectToRoute("book_by_id", ["id" => $request->get('id')]);
    }

    private function bookSetter(
        Book $book,
        Request $request,
        ManagerRegistry $doctrine
    ): void
    {
        $entityManager = $doctrine->getManager();

        $book->setIsbn($request->get('isbn'));
        $book->setTitle($request->get('title'));
        $book->setFirstname($request->get('firstname'));
        $book->setSurname($request->get('surname'));
        $book->setImage($request->get('image'));

        $entityManager->persist($book);
        $entityManager->flush();
    }
 
}
