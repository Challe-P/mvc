<?php

namespace Challe_P\Game\DeckOfCards;

use Challe_P\Game\Card\Card;

class DeckOfCards
{
    protected array $cards = [];
    protected array $drawn = [];
    protected array $suits = array("hearts", "spades", "diamonds", "clubs");
    protected array $values = array("ace", "2", "3", "4", "5", "6", "7", "8", "9", "10", "jack", "queen", "king");

    public function __construct()
    {
        $this->cards = array();
        foreach ($this->suits as $suit) {
            foreach ($this->values as $value) {
                array_push($this->cards, new Card($value, $suit));
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

    public function draw_card(): Card
    {
        array_push($this->drawn, array_shift($this->cards));
        return end($this->drawn);
    }

    public function cards_left(): int
    {
        return sizeof($this->cards);
    }

    public function shuffle_drawn($hands)
    {
        $this->__construct();
        foreach ($hands as $hand) {
            foreach ($hand['hand']->getHand() as $card) {
                foreach ($this->cards as $deckCard) {
                    if ($deckCard->__toString() == $card->__toString()) {
                        $key = array_search($deckCard, $this->cards);
                        unset($this->cards[$key]);
                        array_push($this->drawn, $card);
                    }
                }
            }
        }
        $this->shuffle();
    }
}
