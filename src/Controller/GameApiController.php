<?php

namespace App\Controller;

use Challe_P\Game\CardHand\CardHand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Challe_P\Game\DeckOfCards\DeckOfCards;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\GameController;
use App\Controller\Utils;

class GameApiController extends GameController
{
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

    #[Route('/api/game', name: 'game_api', methods: ['GET'])]
    public function gameApi(
        SessionInterface $session,
        Utils $utils
    ): JsonResponse {
        $hands = $session->get('hands');
        assert(is_array($hands));
        foreach ($hands as &$player) {
            $hand = $player['hand']->__toString();
            $player['hand'] = $hand;
        }
        $state = $session->get('state');
        $cardsLeft = $utils->deckCheck($session)->cardsLeft();
        $hands['State'] = $state;
        $hands['Cards Left'] = $cardsLeft;
        $response = new JsonResponse($hands);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
