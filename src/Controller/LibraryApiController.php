<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\BookRepository;

class LibraryApiController extends AbstractController
{
    #[Route("/api/library/books", name: "library_api")]
    public function libraryApi(
        BookRepository $bookRepository
    ): JsonResponse {
        $books = $bookRepository->findAll();
        $response = $this->json($books);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/library/book/{isbn}", name: "isbn_api")]
    public function isbnApi(
        BookRepository $bookRepository,
        string $isbn
    ): JsonResponse {
        $book = $bookRepository->findByIsbn($isbn);
        $response = $this->json($book);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
