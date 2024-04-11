<?php

namespace Challe_P\Game\TarotDeck;

use Challe_P\Game\Card\Card;
use Challe_P\Game\DeckOfCards\DeckOfCards;

class TarotDeck extends DeckOfCards
{
    protected array $suits = array("wands", "cups", "swords", "pentacles", "major arcana");
    protected array $values = array("ace", "2", "3", "4", "5", "6", "7", "8", "9", "10", "page", "knight", "queen", "king");
    protected array $majorValues = array("0 - The Fool", "I - The Magician", "II - The High Priestess", "III - The Empress", "IV - The Emperor",
    "V - The Hierophant", "VI - The Lovers", "VII - The Chariot", "VIII - Strength", "IX - The Hermit", "X - Wheel of Fortune", "XI - Justice",
    "XII - The Hanged Man", "XIII - Death", "XIV - Temperance", "XV - The Devil", "XVI - The Tower", "XVII - The Star", "XVIII - The Moon",
    "XIX - The Sun", "XX - Judgement", "XXI - The World");

    public function __construct()
    {
        $this->cards = array();
        foreach ($this->suits as $suit) {
            if ($suit != "major arcana") {
                foreach ($this->values as $value) {
                    array_push($this->cards, new Card($value, $suit));
                }
            } else {
                foreach ($this->majorValues as $value) {
                    array_push($this->cards, new Card($value, $suit));
                }
            }
        }
    }
}