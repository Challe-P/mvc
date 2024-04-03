<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
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

    #[Route('/api', name: "api")]
    public function api(): Response
    {
        return $this->render('api.html.twig');
    }

    #[Route("/api/lucky/animal", name: "animal_api")]
    public function luckyAnimal(): Response
    {
        $animals = array('Katt', 'Hund', 'Tiger', 'Liger', 'Lejon', 'Apa', 'Krokodil');
        $animal = array_rand($animals, 1);
        $response = new JsonResponse(array($animals[$animal]));
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/quote", name: 'quote')]
    public function quote(): Response
    {
        $quotes = array('Katter är hemska små varelser, kanske.',
        'Hundar är mycket bra djur.',
        'Tigrar är farliga, men även randiga.',
        'Liger är en blandning mellan ett lejon och en tiger.',
        'Lejon är savannens konung, men snart utdöda.',
        'Apor är fruktansvärda.',
        'Krokodiler sitter ofta i bilar och spelar trumpet.');
        $quote = array_rand($quotes, 1);
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $data = array($quotes[$quote], $date, $time);
        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }


    #[Route("/lucky", name: "lucky")]
    public function lucky(): Response
    {
        $animal = $this->luckyAnimal()->getContent();
        $animal_array = json_decode($animal, true);
        
        return $this->render('lucky.html.twig', ['animal' => $animal_array[0]]);
    }
}
