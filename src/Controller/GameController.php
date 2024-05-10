<?php

namespace App\Controller;

use App\Game\CardHand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Game\DeckOfCards;
use App\Controller\Utils;

/**
 * A controller for the card part of the site
 */
class GameController extends AbstractController
{
    /**
     * Renders the starting point for the card sites.
     */
    #[Route("/card", name: "card")]
    public function init(): Response
    {
        return $this->render('card.html.twig');
    }

    /**
     * Displays a fresh deck of cards on the site.
     */
    #[Route("/card/deck", name: "deck")]
    public function printDeck(
        SessionInterface $session
    ): Response {
        $deck = new DeckOfCards();
        $session->set('deck', $deck);
        return $this->render('deck_print.html.twig', ['deck' => $deck->getCards()]);
    }

    /**
     * Displays a shuffled new deck of cards on the site.
     */
    #[Route("/card/deck/shuffle", name: "shuffleDeck")]
    public function shuffleDeck(
        SessionInterface $session
    ): Response {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('deck', $deck);
        return $this->render('shuffled.html.twig', ['deck' => $deck->getCards()]);
    }
}
