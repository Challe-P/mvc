<?php

namespace App\Game;

class DeckOfCards extends AbstractDeck
{
    /**
     * @var array<string> $suits
     */
    protected array $suits = ["hearts", "spades", "diamonds", "clubs"];
    /**
     * @var array<string> $values
     */
    protected array $values = ["ace", "2", "3", "4", "5", "6", "7", "8", "9", "10", "jack", "queen", "king"];
}
