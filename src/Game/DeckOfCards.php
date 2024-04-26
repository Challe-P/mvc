<?php

namespace Challe_P\Game\DeckOfCards;

use Challe_P\Game\Card\Card;
use Challe_P\Game\CardHand\CardHand;
use Challe_P\Game\Player\Player;
use Challe_P\Game\Exceptions;
use Challe_P\Game\Exceptions\EmptyDeckException;

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
        if (count($this->cards) > 0 and is_array($this->cards)) {
            $card = array_shift($this->cards);
            if ($card != null) {
                array_push($this->drawn, $card);
                return end($this->drawn);
            }
        }
        throw new EmptyDeckException();
    }

    public function cardsLeft(): int
    {
        return sizeof($this->cards);
    }

    /**
     * @param array<Player> $players
     */
    public function shuffleDrawn(array $players): void
    {
        $this->__construct();
        foreach ($players as $player) {
            foreach ($player->getHand()->getHand() as $card) {
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
