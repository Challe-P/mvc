<?php

namespace App\Game;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

use App\Game\CardHand;
use App\Game\Card;
use App\Game\DeckOfCards;


/**
 * Test cases for class Card.
 */
#[CoversClass(Player::class)]
#[UsesClass(CardHand::class)]
#[UsesClass(Card::class)]
#[UsesClass(DeckOfCards::class)]
class PlayerTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreatePlayer(): void
    {
        $player = new Player("testguy", new CardHand);
        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals("testguy", $player->getName());
        $this->assertEquals(0, $player->getScore());
    }

    public function testSetName(): void
    {
        $player = new Player("testguy", new CardHand);
        $this->assertEquals("testguy", $player->getName());
        $player->setName("changeguy");
        $this->assertEquals("changeguy", $player->getName());
    }
    
    public function testSetScore(): void
    {
        $player = new Player("testguy", new CardHand);
        $this->assertEquals(0, $player->getScore());
        $player->setScore(42);
        $this->assertEquals(42, $player->getScore());
    }

    public function testGetHand(): void
    {
        $player = new Player('testguy', new CardHand(2));
        $this->assertInstanceOf(CardHand::class, $player->getHand());
        $this->assertEquals(2, sizeof($player->getHand()->getHand()));
    }

    public function testSetHand(): void
    {
        $player = new Player('testguy', new CardHand);
        $this->assertInstanceOf(CardHand::class, $player->getHand());
        $this->assertEquals(5, sizeof($player->getHand()->getHand()));
        $player->setHand(new CardHand(2));
        $this->assertInstanceOf(CardHand::class, $player->getHand());
        $this->assertEquals(2, sizeof($player->getHand()->getHand()));
    }
}