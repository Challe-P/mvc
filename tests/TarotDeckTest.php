<?php

namespace App\Game\TarotDeck;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use App\Game\Card\Card;

/**
 * Test cases for class CardHand.
 */
#[CoversClass(TarotDeck::class)]
#[UsesClass(Card::class)]
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
