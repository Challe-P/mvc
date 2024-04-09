<?php

namespace Challe_P\Game\DeckOfCards;
use Challe_P\Game\Card\Card;

class DeckOfCards
{
    protected $cards;
    protected $suits = Array("hearts", "spades", "diamonds", "clubs");
    protected $values = Array("A", "2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K");

    public function __construct()
    {
        $this->cards = Array();
        foreach ($this->suits as $suit) {
            foreach ($this->values as $value) {
                array_push($this->cards, new Card($suit, $value)); 
            }
        };
    }

    public function print_all(): string
    {
        $output = "";
        foreach ($this->cards as $card) {
            $output .= $card->getValue() . "\n";
        };
        return $output;
    }

    public function get_cards(): array
    {
        return $this->cards;
    }

    public function shuffle()
    {
        shuffle($this->cards);
    }
}
