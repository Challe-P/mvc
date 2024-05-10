<?php

namespace App\Controller;

use App\Game\CardHand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Game\DeckOfCards;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\GameController;
use App\Controller\Utils;

class GameApiController extends GameController
{
    /**
     * Creates a new deck of cards, then returns it as a json-object in json-response.
     */
    #[Route("/api/deck", name: "deck_api")]
    public function deckApi(
        Utils $utils
    ): JsonResponse {
        $deck = new DeckOfCards();
        $cardoutput = [];
        $cardoutput['deck'] = $utils->deckToStringArray($deck->getCards());
        $response = new JsonResponse($cardoutput);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
     * Creates a new deck of cards, shuffles it,
     * then returns it as a json-object in json-response.
     */
    #[Route("/api/deck/shuffle", name: "deck_shuffle_api", methods: ['POST'])]
    public function deckShuffleApi(
        SessionInterface $session,
        Utils $utils
    ): JsonResponse {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $session->set('deck', $deck);
        $cardoutput = [];
        $cardoutput['deck'] = $utils->deckToStringArray($deck->getCards());
        $response = new JsonResponse($cardoutput);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
     * Draws a card from the deck stored in the session, or a new deck if there's not a deck
     * in the session.
     */
    #[Route("/api/deck/draw", name: "deck_draw_api", methods: ['POST'])]
    public function drawApi(
        SessionInterface $session,
        Request $request,
        Utils $utils
    ): JsonResponse {
        $amount = $request->get('amount');
        $amount = ($amount == null) ? 1 : $amount;
        $deck = $utils->deckCheck($session);
        $output = ["Not enough cards left in deck"];
        if ($amount <= $deck->cardsLeft()) {
            $cards = [];
            for ($i = 0; $i < $amount; $i++) {
                $cards[$i] = $deck->drawCard();
            }
            unset($output);
            $output['Cards'] = $utils->deckToStringArray($cards);
        }
        $output["Cards left:"] = $deck->cardsLeft();
        $response = new JsonResponse($output);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/deal", name: "deck_deal_api", methods: ['POST'])]
    public function dealApi(
        SessionInterface $session,
        Request $request,
        Utils $utils
    ): JsonResponse {
        $players = $request->get('players');
        $cards = $request->get('cards');
        assert(is_int($cards));
        $deck = $utils->deckCheck($session);
        $output = ["Not enough cards left in deck"];
        if ($cards * $players <= $deck->cardsLeft()) {
            unset($output);
            $output = [];
            for ($i = 0; $i < $players; $i++) {
                $hand = new CardHand($cards, $deck);
                $playerNum = $i + 1;
                $playerString = "Player " . $playerNum . ":";
                $output[$playerString] = $utils->deckToStringArray($hand->getHand());
            }
        }
        $output['Cards left:'] = $deck->cardsLeft();
        $response = new JsonResponse($output);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
