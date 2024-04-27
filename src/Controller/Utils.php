<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Challe_P\Game\DeckOfCards\DeckOfCards;
use Challe_P\Game\CardHand\CardHand;
use Challe_P\Game\Card\Card;
use Challe_P\Game\Player\Player;
use InvalidArgumentException;

class Utils
{
    public function deckCheck(
        SessionInterface $session
    ): DeckOfCards {
        if ($session->get('deck') === null) {
            $deck = new DeckOfCards();
            $session->set("deck", $deck);
            assert($deck instanceof DeckOfCards);
            return $deck;
        }
        $deck = $session->get('deck');
        assert($deck instanceof DeckOfCards);
        return $deck;
    }

    /**
    * @return array<Player>
    */
    public function playerCheck(
        SessionInterface $session
    ): ?array {
        if ($session->get('players') === null) {
            return null;
        }
        $players = $session->get('players');
        if (is_array($players) && count($players) > 0 && $players[0] instanceof Player) {
            return $players;
        }
        return null;
    }

    /**
    * @param array<Card> $deck
    * @return array<string>
    */
    public function deckToStringArray(
        array $deck
    ): array {
        $output = [];
        foreach ($deck as $card) {
            array_push($output, $card->__toString());
        }
        return $output;
    }
}