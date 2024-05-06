<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Test cases for class Card.
 */
#[CoversClass(Card::class)]
class CardTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateCard(): void
    {
        $card = new Card();
        $card->getValue();
        $this->assertInstanceOf(Card::class, $card, 'Card is an instance of Card class');
        $this->assertEquals("ace", $card->getValue());
        $this->assertEquals("spades", $card->getSuit());
    }
    /**
     * Tests image path-getter
     */
    public function testgetImagePath(): void
    {
        $card = new Card();
        $this->assertEquals("img/cards/fronts/spades_ace.svg", $card->getImagePath());
    }
    /**
     * Tests __toString()
     */
    public function testToString(): void
    {
        $card = new Card();
        $this->assertEquals("ace of spades", (string)$card);
    }
}