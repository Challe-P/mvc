<?php

namespace Challe_P\Game\DeckOfCards;

use Challe_P\Game\Card\Card;
use Challe_P\Game\CardHand\CardHand;

class DeckOfCards
{
    /**
     * @var array<Card> $cards
     */
    protected array $cards = [];
    /**
     * @var array<Card> $drawn
     */
    protected array $drawn = [];
    /**
     * @var array<string> $suits
     */
    protected array $suits = ["hearts", "spades", "diamonds", "clubs"];
    /**
     * @var array<string> $values
     */
    protected array $values = ["ace", "2", "3", "4", "5", "6", "7", "8", "9", "10", "jack", "queen", "king"];

    public function __construct()
    {
        $this->cards = array();
        foreach ($this->suits as $suit) {
            foreach ($this->values as $value) {
                array_push($this->cards, new Card($value, $suit));
            }
        };
    }

    public function printAll(): string
    {
        $output = "";
        foreach ($this->cards as $card) {
            $output .= $card->getValue() . "\n";
        };
        return $output;
    }

    /**
     * @return array<Card>
     */
    public function getCards(): array
    {
        return $this->cards;
    }

    public function shuffle(): void
    {
        shuffle($this->cards);
    }

    public function drawCard(): Card
    {
        array_push($this->drawn, array_shift($this->cards));
        return end($this->drawn);
    }

    public function cardsLeft(): int
    {
        return sizeof($this->cards);
    }

    /**
     *
     */
    public function shuffleDrawn(array $hands): void
    {
        $this->__construct();
        echo var_dump($hands);
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
