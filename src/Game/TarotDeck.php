<?php

namespace App\Game\TarotDeck;

use App\Game\Card\Card;
use App\Game\DeckOfCards\DeckOfCards;

class TarotDeck extends DeckOfCards
{
    /**
     * @var array<string> $suits
     */
    protected array $suits = ["wands", "cups", "swords", "pentacles"];
    /**
     * @var array<string> $values
     */
    protected array $values = ["ace", "2", "3", "4", "5", "6", "7", "8", "9", "10", "page", "knight", "queen", "king"];
    /**
     * @var array<string> $majorValues
     */
    protected array $majorValues = ["0 - The Fool", "I - The Magician", "II - The High Priestess", "III - The Empress", "IV - The Emperor",
    "V - The Hierophant", "VI - The Lovers", "VII - The Chariot", "VIII - Strength", "IX - The Hermit", "X - Wheel of Fortune", "XI - Justice",
    "XII - The Hanged Man", "XIII - Death", "XIV - Temperance", "XV - The Devil", "XVI - The Tower", "XVII - The Star", "XVIII - The Moon",
    "XIX - The Sun", "XX - Judgement", "XXI - The World"];

    public function __construct()
    {
        $this->cards = array();
        foreach ($this->suits as $suit) {
            foreach ($this->values as $value) {
                array_push($this->cards, new Card($value, $suit));
            }
        }
        foreach ($this->majorValues as $value) {
            array_push($this->cards, new Card($value, "major arcana"));
        }
    }
}
