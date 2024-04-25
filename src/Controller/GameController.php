<?php

namespace App\Controller;

use Challe_P\Game\CardHand\CardHand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Challe_P\Game\DeckOfCards\DeckOfCards;
use App\Controller\Utils;

class GameController extends AbstractController
{
    #[Route("/card", name: "card")]
    public function init(): Response
    {
        return $this->render('card.html.twig');
    }

    #[Route("/card/deck", name: "deck")]
    public function printDeck(
        SessionInterface $session
    ): Response {
        $deck = new DeckOfCards();
        $session->set('deck', $deck);
        return $this->render('deck_print.html.twig', ['deck' => $deck->getCards()]);
    }

    #[Route("/card/deck/shuffle", name: "shuffleDeck")]
    public function shuffleDeck(
        SessionInterface $session
    ): Response {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('deck', $deck);
        return $this->render('shuffled.html.twig', ['deck' => $deck->getCards()]);
    }

    #[Route("/card/deck/draw/shuffle", name: "shuffleDeckToDraw")]
    public function shuffleDeckToDraw(
        SessionInterface $session
    ): Response {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('deck', $deck);
        return $this->redirectToRoute('draw');
    }

    #[Route("/card/deck/draw", name: "draw")]
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

    #[Route("/card/deck/draw/:{amount<\d+>}", name: "drawAmount")]
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

    #[Route("/card/deck/draw/:", name: "drawAmountPost", methods: ["POST"])]
    public function drawAmountPost(
        Request $request
    ): RedirectResponse {
        $amount = $request->get('amount');
        return $this->redirectToRoute('drawAmount', ['amount' => $amount]);
    }

    #[Route("/card/deck/deal/:{players<\d+>}/:{cards<\d+>}", name: "deal")]
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

    #[Route("/card/deck/deal/", name: "dealPost", methods: ["POST"])]
    public function dealPost(
        Request $request
    ): RedirectResponse {
        $players = $request->get('players');
        $cards = $request->get('cards');
        return $this->redirectToRoute('deal', ['players' => $players, 'cards' => $cards]);
    }
}
