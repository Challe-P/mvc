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
        $deck = $this->deckCheck($session);
        return $this->render('card.html.twig');
    }

    #[Route("/card/deck", name: "deck")]
    public function printDeck(
        SessionInterface $session
    ): Response
    {
        $deck = new DeckOfCards();
        $session->set('deck', $deck);
        return $this->render('deck_print.html.twig', ['deck' => $deck->get_cards()]);
    }

    #[Route("/card/deck/shuffle", name: "shuffleDeck")]
    public function shuffleDeck(
        SessionInterface $session
    ): Response
    {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('deck', $deck);
        return $this->render('shuffled.html.twig', ['deck' => $deck->get_cards()]);
    }
    
    #[Route("/card/deck/draw", name: "draw")]
    public function drawCard(
        SessionInterface $session
    ): Response
    {
        $deck = $this->deckCheck($session);
        $cardsLeft = $deck->cards_left();
        if ($cardsLeft != 0) {
            $card = Array($deck->draw_card());
            $cardsLeft = $deck->cards_left();
        } else {
            $this->addFlash(
                'warning',
                "There's no cards left."
            );
            $card = null;
        }

        return $this->render('draw.html.twig', ['cardsLeft' => $cardsLeft, 'cards' => $card]);
    }

    #[Route("/card/deck/draw/:{amount<\d+>}", name: "drawAmount")]
    public function drawAmount(
        SessionInterface $session,
        int $amount
    ): Response
    {
        $deck = $this->deckCheck($session);
        $cardsLeft = $deck->cards_left();
        if ($cardsLeft >= $amount) {
            $cards = [];
            for ($i = 0; $i < $amount; $i++) {
                array_push($cards, $deck->draw_card());
            }
            $cardsLeft = $deck->cards_left();
        } else {
            $this->addFlash(
                'warning',
                "There's not enough cards left."
            );
            $cards = null;
        }
        return $this->render('draw.html.twig', ['cardsLeft' => $cardsLeft, 'cards' => $cards]);
    }

    #[Route("/card/deck/draw/:", name: "drawAmountPost", methods: ["POST"])]
    public function drawAmountPost(
        Request $request 
    )
    {
        $amount = $request->get('amount');
        return $this->redirectToRoute('drawAmount', ['amount' => $amount]);
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
    
    public function deckCheck(
        SessionInterface $session
    ): DeckOfCards
    {
        if ($session->get('deck') === null) {
            $deck = new DeckOfCards();
            $session->set("deck", $deck);
        } else {
            $deck = $session->get('deck');
        }
        return $deck;
    }
}