<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Challe_P\Game\DeckOfCards\DeckOfCards;

class GameController extends AbstractController
{
    #[Route("/card", name: "card")]
    public function init(
        SessionInterface $session
    ): Response
    {
        $deck = new DeckOfCards();
        $session->set("deck", $deck);
        return $this->render('card.html.twig');
    }

    #[Route("/card/deck", name: "deck")]
    public function printDeck(
        SessionInterface $session
    ): Response
    {
        $deck = $session->get('deck');
        $output = $deck->print_all();
        $deckArray = $deck->get_cards();
        return $this->render('deck_print.html.twig', ['output' => $output, 'deck' => $deckArray]);
    }

    #[Route("/card/deck/shuffle", name: "shuffleDeck")]
    public function shuffleDeck(
        SessionInterface $session
    ): Response
    {
        // Hämta decken i sessionen, blanda.
        return $this->render('shuffled.html.twig');
    }
    
    #[Route("/card/deck/draw", name: "drawDeck")]
    public function drawCard(
        SessionInterface $session
    ): Response
    {
        // Dra ett kort från leken
        return $this->render('draw.html.twig');
    }

    #[Route('/session', name: "session")]
    public function session(
        SessionInterface $session
    ): Response
    {
        $data = [
          'session' =>  $session->all()
        ];

        return $this->render('session.html.twig', $data);
    }

    #[Route('/session/delete', name: "sessionDelete")]
    public function clear(
        SessionInterface $session
    ): Response
    {
        $session->clear();
        $this->addFlash(
            'notice',
            'The session was deleted'
        );
        // lägg till alert här
        return $this->redirectToRoute('session');
    }
    
}