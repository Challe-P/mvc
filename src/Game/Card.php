<?php

namespace App\Game\Card;

/**
 * A Card class for use in card games.
 */
class Card
{
    /** The value of the card. E.g. "ace", "2" or "queen" */
    protected string $value;
    /** The suit of the card. E.g. "spades", "hearts", "diamonds" or "clubs".*/
    protected string $suit;

    /**
     * Creates a new Card Object. Defualt value is ace, default suit is spades.
     */
    public function __construct(string $value = "ace", string $suit = "spades")
    {
        $this->value = $value;
        $this->suit = $suit;
    }

    /**
     * Gets the image path for a card.
     * @return string The image path.
     */
    public function getImagePath(): string
    {
        return 'img/cards/fronts/' . $this->suit . "_" . $this->value . ".svg";
    }

    /**
     * Gets the cards value.
     * @return string The value.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Gets the cards suit.
     * @return string The suit.
     */
    public function getSuit(): string
    {
        return $this->suit;
    }

    /**
     * Returns the cards value and suit as a string, in the format "ace of spades".
     * @return string The card as a string.
     */
    public function __toString(): string
    {
        return $this->value . " of " . $this->suit;
    }
}
