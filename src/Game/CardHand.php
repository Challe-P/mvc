<?php

namespace Challe_P\Game\CardHand;

use Challe_P\Game\DeckOfCards\DeckOfCards;

class CardHand
{
    private array $cards = [];

    public function __construct(int $noOfCards = 5, DeckOfCards $deck = new DeckOfCards())
    {
        for ($i = 0; $i < $noOfCards; $i++) {
            array_push($this->cards, $deck->draw_card());
        }
    }

    public function draw($deck) {
        array_push($this->cards, $deck->draw_card());
        return;
    }

    public function getHand(): array
    {
        return $this->cards;
    }

    public function __toString(): string
    {
        return implode(", ", $this->cards);
    }
}
