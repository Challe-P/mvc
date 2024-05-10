<?php

namespace App\Controller;

use App\Game\CardHand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Game\DeckOfCards;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Utils;
use App\Game\Card;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TypeError;
use ValueError;

class GameApiController extends AbstractController
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
        return $this->responseEncoder($cardoutput);
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
        return $this->responseEncoder($cardoutput);
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
        try {
            $amount = $this->intCheck($request->get('amount'));
        } catch (TypeError) {
            $amount = 1;
        }
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
        return $this->responseEncoder($output);
    }

    /**
     * Deals an amount of cards to the amount of players specified.
     */
    #[Route("/api/deck/deal", name: "deck_deal_api", methods: ['POST'])]
    public function dealApi(
        SessionInterface $session,
        Request $request,
        Utils $utils
    ): JsonResponse {
        try {
            list($players, $cards) = [$this->intCheck($request->get('players')),
            $this->intCheck($request->get('cards'))];
        } catch (TypeError) {
            $output = ["Players and/or cards not an valid integer"];
            return $this->responseEncoder($output);
        }
        $deck = $utils->deckCheck($session);

        if ($cards * $players > $deck->cardsLeft()) {
            $output = ["Not enough cards left in deck"];
            return $this->responseEncoder($output);
        }

        $output = [];
        for ($i = 0; $i < $players; $i++) {
            $hand = new CardHand($cards, $deck);
            $playerNum = $i + 1;
            $playerString = "Player " . $playerNum . ":";
            $output[$playerString] = $utils->deckToStringArray($hand->getHand());
        }
        $output['Cards left:'] = $deck->cardsLeft();
        return $this->responseEncoder($output);
    }

    /**
     * Encodes responses with JSON pretty print.
     */
    private function responseEncoder(
        mixed $output
    ): JsonResponse {
        $response = new JsonResponse($output);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
     * Checks if input is numeric and casts it to int.
     */
    private function intCheck(
        mixed $param
    ): int {
        if (is_numeric($param)) {
            return (int) $param;
        }
        throw new TypeError();
    }
}
