<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for class CardHand.
 */
class TarotDeckTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateTarotDeck(): void
    {
        $deck = new TarotDeck();
        $this->assertInstanceOf(TarotDeck::class, $deck);
        $this->assertEquals(78, sizeof($deck->getCards()));
    }
}
