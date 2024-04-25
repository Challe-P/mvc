<?php

namespace App\Controller;

use Challe_P\Game\CardHand\CardHand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Challe_P\Game\DeckOfCards\DeckOfCards;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\GameController;

class GameApiController extends GameController
{
    #[Route("/api/deck", name: "deck_api")]
    public function deckApi(): JsonResponse
    {
        $deck = new DeckOfCards();
        $strings['deck'] = $this->deckToStringArray($deck->get_cards());
        $response = new JsonResponse($strings);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/shuffle", name: "deck_shuffle_api", methods: ['POST'])]
    public function deckShuffleApi(
        SessionInterface $session
    ): JsonResponse {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('deck', $deck);
        $strings['deck'] = $this->deckToStringArray($deck->get_cards());
        $response = new JsonResponse($strings);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/draw", name: "deck_draw_api", methods: ['POST'])]
    public function draw_api(
        SessionInterface $session,
        Request $request
    ): JsonResponse {
        $amount = $request->get('amount');
        $amount = ($amount == null) ? 1 : $amount;
        $deck = $this->deckCheck($session);

        if ($amount <= $deck->cards_left()) {
            $cards = [];
            for ($i = 0; $i < $amount; $i++) {
                $cards[$i] = $deck->draw_card();
            }
            $strings['Cards'] = $this->deckToStringArray($cards);
        } else {
            $strings = ["Not enough cards left in deck"];
        }
        $strings["Cards left:"] = $deck->cards_left();
        $response = new JsonResponse($strings);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/deal", name: "deck_deal_api", methods: ['POST'])]
    public function deal_api(
        SessionInterface $session,
        Request $request
    ): JsonResponse {
        $players = $request->get('players');
        $cards = $request->get('cards');
        $deck = $this->deckCheck($session);
        $strings = [];

        if ($cards * $players <= $deck->cards_left()) {
            for ($i = 0; $i < $players; $i++) {
                $hand = new CardHand($cards, $deck);
                $playerString = "Player " . $i + 1 . ":";
                $strings[$playerString] = $this->deckToStringArray($hand->getHand());
            }
        } else {
            $strings = ["Not enough cards left in deck"];
        }
        $strings['Cards left:'] = $deck->cards_left();
        $response = new JsonResponse($strings);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route('/api/game', name: 'game_api', methods: ['GET'])]
    public function game_api(
        SessionInterface $session,
    ): JsonResponse {
        $hands = $session->get('hands');
        foreach ($hands as &$player) {
            $hand = $player['hand']->__toString();
            $player['hand'] = $hand;
        }
        $state = $session->get('state');
        $cardsLeft = $this->deckCheck($session)->cards_left();
        $hands['State'] = $state;
        $hands['Cards Left'] = $cardsLeft;
        $response = new JsonResponse($hands);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    public function deckToStringArray(
        array $deck
    ): array {
        $strings = [];
        foreach ($deck as $card) {
            array_push($strings, $card->__toString());
        }
        return $strings;
    }
}
