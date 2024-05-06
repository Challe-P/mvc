<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Book;

class LibraryController extends AbstractController
{
    #[Route('/library', name: 'app_library')]
    public function index(): Response
    {
        return $this->render('library.html.twig', [
            'controller_name' => 'LibraryController',
        ]);
    }

    // Routes (get-formulär => post) för att lägga till en bok C

    /**
     * Exempelkod: https://github.com/dbwebb-se/mvc/tree/main/example/symfony-doctrine
     * #[Route('/product/create', name: 'product_create')]
     * public function createProduct(
    *    ManagerRegistry $doctrine
    *): Response {
    *    $entityManager = $doctrine->getManager();

    *    $product = new Product();
    *    $product->setName('Keyboard_num_' . rand(1, 9));
    *    $product->setValue(rand(100, 999));

    *    // tell Doctrine you want to (eventually) save the Product
    *    // (no queries yet)
    *    $entityManager->persist($product);

    *    // actually executes the queries (i.e. the INSERT query)
    *    $entityManager->flush();

    *    return new Response('Saved new product with id '.$product->getId());
    *}
     */

    // Route (GET) för att visa en bok R

    // Route (GET) för att visa alla böcker R

    // Route (GET-formulär => post) för att ändra i en bok

    // Route (GET-formulär => post) för att ta bort en bok D

 
}
