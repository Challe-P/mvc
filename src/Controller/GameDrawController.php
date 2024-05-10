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
 * A controller for the drawing part of the card site.
 */
class GameDrawController extends AbstractController
{
    /**
     * Shuffles a deck, then redirects to draw. Used when there's no cards left in the deck.
     */
    #[Route("/card/deck/draw/shuffle", name: "shuffleDeckToDraw", methods: ['GET'])]
    public function shuffleDeckToDraw(
        SessionInterface $session
    ): Response {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('deck', $deck);
        return $this->redirectToRoute('draw');
    }

    /**
     * Draws a card from the deck stored in the session.
     */
    #[Route("/card/deck/draw", name: "draw", methods: ['GET'])]
    public function drawCard(
        SessionInterface $session,
        Utils $utils
    ): Response {
        $deck = $utils->deckCheck($session);
        $cardsLeft = $deck->cardsLeft();
        if ($cardsLeft != 0) {
            $card = array($deck->drawCard());
            $cardsLeft = $deck->cardsLeft();
            return $this->render('draw.html.twig', ['cardsLeft' => $cardsLeft, 'cards' => $card]);
        }
        $this->addFlash(
            'warning',
            "There's not enough cards left."
        );
        $card = null;
        return $this->render('draw.html.twig', ['cardsLeft' => $cardsLeft, 'cards' => $card]);
    }

    /**
     * Draws the amount of cards specified in the URL from the deck saved in session.
     */
    #[Route("/card/deck/draw/:{amount<\d+>}", name: "drawAmount", methods: ['GET'])]
    public function drawAmount(
        SessionInterface $session,
        Utils $utils,
        int $amount
    ): Response {
        $deck = $utils->deckCheck($session);
        $cardsLeft = $deck->cardsLeft();
        if ($cardsLeft >= $amount) {
            $cards = [];
            for ($i = 0; $i < $amount; $i++) {
                array_push($cards, $deck->drawCard());
            }
            $cardsLeft = $deck->cardsLeft();
            return $this->render('draw.html.twig', ['cardsLeft' => $cardsLeft, 'cards' => $cards]);
        }
        $this->addFlash(
            'warning',
            "There' not enough cards left."
        );
        $cards = null;
        return $this->render('draw.html.twig', ['cardsLeft' => $cardsLeft, 'cards' => $cards]);
    }

    /**
     * Draws the amount of cards sent in post, by redirecting the request to the GET-path
     */
    #[Route("/card/deck/draw/:", name: "drawAmountPost", methods: ["POST"])]
    public function drawAmountPost(
        Request $request
    ): RedirectResponse {
        $amount = $request->get('amount');
        return $this->redirectToRoute('drawAmount', ['amount' => $amount]);
    }

    /**
     * Deals the number of cards to the number of players specified in the URL.
     */
    #[Route("/card/deck/deal/:{players<\d+>}/:{cards<\d+>}", name: "deal", methods: ['GET'])]
    public function deal(
        SessionInterface $session,
        Utils $utils,
        int $players,
        int $cards
    ): Response {
        $hands = [];
        $deck = $utils->deckCheck($session);
        $totalCards = $players * $cards;
        $cardsLeft = $deck->cardsLeft();
        if ($totalCards <= $cardsLeft) {
            for ($i = 0; $i < $players; $i++) {
                array_push($hands, new CardHand($cards, $deck));
            }
            return $this->render('deal.html.twig', ['hands' => $hands, 'cardsLeft' => $cardsLeft, 'totalCards' => $totalCards]);
        }
        $this->addFlash(
            'warning',
            "There' not enough cards left."
        );
        $cardsLeft = $deck->cardsLeft();

        return $this->render('deal.html.twig', ['hands' => $hands, 'cardsLeft' => $cardsLeft, 'totalCards' => $totalCards]);
    }

    /**
     * Deals the number of cards to the number of players specified in the POST request.
     * Redirects to the GET-path.
     */
    #[Route("/card/deck/deal/", name: "dealPost", methods: ["POST"])]
    public function dealPost(
        Request $request
    ): RedirectResponse {
        $players = $request->get('players');
        $cards = $request->get('cards');
        return $this->redirectToRoute('deal', ['players' => $players, 'cards' => $cards]);
    }
}
