<?php

namespace App\Game\CardHand;

use App\Game\DeckOfCards\DeckOfCards;
use App\Game\Card\Card;

class CardHand
{
    /**
     * @var array<Card> An array containing cards.
     */
    private array $cards = [];

    public function __construct(int $noOfCards = 5, DeckOfCards $deck = new DeckOfCards())
    {
        for ($i = 0; $i < $noOfCards; $i++) {
            array_push($this->cards, $deck->drawCard());
        }
    }

    public function draw(DeckOfCards $deck): void
    {
        array_push($this->cards, $deck->drawCard());
    }

    /**
     * @return array<Card>
     */
    public function getHand(): array
    {
        return $this->cards;
    }

    public function __toString(): string
    {
        return implode(", ", $this->cards);
    }
}
