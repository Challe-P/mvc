<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Book;

/**
 * A controller class for the library route.
 */
class LibraryController extends AbstractController
{
    /**
     * Starting screen for library, with all the books currently in the database shown.
     */
    #[Route('/library', name: 'app_library')]
    public function index(
        BookRepository $bookRepository
    ): Response {
        $books = $bookRepository->findAll();
        return $this->render('library/library.html.twig', ['books' => $books]);
    }

    /**
     * Shows a specific book, finding it by id.
     */
    #[Route('/library/show/{id}', name: 'book_by_id')]
    public function showBookById(
        BookRepository $bookRepository,
        int $id
    ): Response {
        $book = $bookRepository->find($id);
        return $this->render('library/show.html.twig', ['book' => $book]);
    }

    /**
     * A route that leads to a form where you can create a new book.
     */
    #[Route('/library/create_form', name: "book_create_form")]
    public function createBookForm(): Response
    {
        return $this->render("library/create.html.twig");
    }

    /**
     * Route to handle form data from createBookForm. Creates a book entity.
     */
    #[Route('/library/book_create', name: "book_create", methods: ["POST"])]
    public function createBook(
        Request $request,
        ManagerRegistry $doctrine
    ): Response {
        $book = new Book();
        $this->bookSetter($book, $request, $doctrine);
        return $this->redirectToRoute('app_library');
    }

    /**
     * A route that leads to a form where you can update a book.
     */
    #[Route('/library/update_form/{id}', name: "book_update_form")]
    public function updateBookForm(
        BookRepository $bookRepository,
        int $id
    ): Response {
        $book = $bookRepository->find($id);
        return $this->render('library/update.html.twig', ["book" => $book]);
    }

    /**
     * Route to handle update data. Updates the book, if found.
     */
    #[Route('/library/book_update', name: "book_update", methods: ["POST"])]
    public function updateBook(
        Request $request,
        BookRepository $bookRepository,
        ManagerRegistry $doctrine
    ): Response {
        $book = $bookRepository->find($request->get("id"));
        if ($book instanceof Book) {
            $this->bookSetter($book, $request, $doctrine);
            return $this->redirectToRoute("book_by_id", ["id" => $request->get('id')]);
        }
        $this->addFlash(
            'warning',
            'No book found'
        );
        return $this->redirectToRoute('app_library');
    }

    /**
     * A route that deletes a book, if it is found, then redirects to start screen.
     */
    #[Route('/library/delete', name: "book_delete", methods: ["POST"])]
    public function deleteBook(
        Request $request,
        ManagerRegistry $doctrine
    ): Response {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($request->get('id'));

        if (!$book) {
            throw $this->createNotFoundException(
                'No product found for id ' . $book
            );
        }

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('app_library');
    }

    /**
     * A function to update information about a book. Contains checkers to assure data is of right type.
     */
    private function bookSetter(
        Book $book,
        Request $request,
        ManagerRegistry $doctrine
    ): void {
        $entityManager = $doctrine->getManager();
        $isbn = $request->get('isbn');
        $title = $request->get('title');
        $firstname = $request->get('firstname');
        $surname = $request->get('surname');
        $image = $request->get('image');

        if (is_string($isbn)) {
            $book->setIsbn($isbn);
        }
        if (is_string($title)) {
            $book->setTitle($title);
        }
        if (is_string($firstname)) {
            $book->setFirstname($firstname);
        }
        if (is_string($surname)) {
            $book->setSurname($surname);
        }
        if (is_string($image)) {
            $book->setImage($image);
        }

        $entityManager->persist($book);
        $entityManager->flush();
    }

}
