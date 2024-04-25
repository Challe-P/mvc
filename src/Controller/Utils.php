<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Challe_P\Game\DeckOfCards\DeckOfCards;
use Challe_P\Game\CardHand\CardHand;
use Challe_P\Game\Card\Card;
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
    * @return array<string, CardHand>
    * #TODO fix $hands !!! - new class
    */
    public function handCheck(
        SessionInterface $session
    ): array {
        if ($session->get('hands') === null) {
            $hands = [];
            $session->set("hands", $hands);
            return $hands;
        }
        $hands = $session->get('hands');
        return $hands;
    }

    /**
    * @param array<mixed> $hands
    */
    private function validateHands(array $hands): bool
    {
        foreach ($hands as $key => $value) {
            if (!is_string($key) || !$value instanceof CardHand) {
                return false;
            }
        }
        return true;
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
