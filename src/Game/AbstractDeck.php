<?php

namespace App\Game;

use App\Game\Card;
use App\Game\Exceptions\EmptyDeckException;

/**
 * An abstract deck of cards, with methods for using it.
 */
abstract class AbstractDeck
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
    protected array $suits = [];
    /**
     * @var array<string> $values
     */
    protected array $values = [];

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
            $output .= $card . "\n";
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
