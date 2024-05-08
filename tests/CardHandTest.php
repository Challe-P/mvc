<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;
use App\Game\DeckOfCards;
use App\Game\Card;

/**
 * Test cases for class CardHand.
 */
class CardHandTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateCardHand(): void
    {
        $cardHand = new CardHand();
        $this->assertInstanceOf(CardHand::class, $cardHand);
        $this->assertEquals(5, sizeof($cardHand->getHand()));
    }
    
    public function testDraw(): void
    {
        $cardHand = new CardHand();
        $cardHand->draw(new DeckOfCards());        
        $this->assertEquals("ace of hearts, 2 of hearts, 3 of hearts, 4 of hearts, 5 of hearts, ace of hearts",
        (string)$cardHand);
    }

    public function testRemoveCard(): void
    {
        $cardHand = new CardHand(5);
        $cardHand->removeCard(0);
        $this->assertEquals(4, sizeof($cardHand->getHand()));
        $this->assertEquals("2 of hearts", $cardHand->getHand()[0]);
    }

    public function testAddCard(): void
    {
        $cardHand = new CardHand(0);
        $this->assertEquals(0, sizeof($cardHand->getHand()));
        $cardHand->addCard(new Card("7", "hearts"));
        $this->assertEquals(1, sizeof($cardHand->getHand()));
        $this->assertEquals("7 of hearts", $cardHand->getHand()[0]);
    }
}
