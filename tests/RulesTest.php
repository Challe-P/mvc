<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;

use App\Game\CardHand;
use App\Game\Card;
use App\Game\DeckOfCards;

/**
 * Test cases for class Rules.
 */
class RulesTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateRules(): void
    {
        $rules = new Rules;
        $this->assertInstanceOf(Rules::class, $rules);
    }

    public function testCreateCustom(): void
    {
        $rules = new Rules(40, ['jack' => 30, 'queen' => 20, 'king' => 10], 10, 10);
        $this->assertInstanceOf(Rules::class, $rules);
    }

    public function testTranslator(): void
    {
        $rules = new Rules;
        $cardHand = new CardHand;
        $score = $rules->translator($cardHand);
        $this->assertEquals(15, $score);
    }

    public function testTranslatorMax(): void
    {
        $rules = new Rules;
        $cardHand = new CardHand(20);
        $score = $rules->translator($cardHand);
        $this->assertEquals(0, $score);
    }
    
    public function testTranslatorNoAces(): void
    {
        $rules = new Rules;
        $cardHand = new CardHand;
        $cardHand->removeCard(0);
        $score = $rules->translator($cardHand);
        $this->assertEquals(14, $score);
    }

    public function testMaxAces(): void
    {
        $rules = new Rules;
        $cardHand = new CardHand(1);
        $cardHand->addCard(new Card("ace", "spades"));
        $cardHand->addCard(new Card("ace", "diamonds"));
        $cardHand->addCard(new Card("ace", "clubs"));
        $score = $rules->translator($cardHand);
        $this->assertEquals(17, $score);
        $cardHand->addCard(new Card("5", "spades"));
        $score = $rules->translator($cardHand);
        $this->assertEquals(9, $score);
    }
}
