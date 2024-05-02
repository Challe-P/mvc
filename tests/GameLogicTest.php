<?php

namespace App\Game\GameLogic;

use App\Game\DeckOfCards\DeckOfCards;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use App\Game\Card\Card;
use App\Game\Player\Player;
use App\Game\CardHand\CardHand;
use App\Game\Rules\Rules;

/**
 * Test cases for class CardHand.
 */
#[CoversClass(GameLogic::class)]
#[UsesClass(Card::class)]
#[UsesClass(Player::class)]
#[UsesClass(DeckOfCards::class)]
#[UsesClass(CardHand::class)]
#[UsesClass(Rules::class)]
class GameLogicTest extends TestCase
{
    private GameLogic $gameLogic;
    private array $players;
    private DeckOfCards $deck;  

    public function setUp(): void
    {
        $this->gameLogic = new GameLogic();
        $this->players = [];
        $this->deck = new DeckOfCards();
    }
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateGameLogic(): void
    {
        $logic = new GameLogic();
        $this->assertInstanceOf(Gamelogic::class, $logic);
    }

    /**
     * Tests plays first two commands, and when the player gets over the max score.
     */
    public function testPlay(): void
    {
        [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "none");
        [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "first");
        $this->assertEquals("first", $state);
        $this->assertEquals($this->players[0]->getName(), "player");
        [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "draw");
        $this->assertEquals($this->players[0]->getScore(), 16);
        for ($i = 0; $i < 5; $i++) {
            [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "draw");
        }
        $this->assertEquals("Bank wins", $state);
        $this->assertEquals($this->players[0]->getScore(), 0);
    }

    public function testPlayHoldBank(): void 
    {
        [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "first");
        [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "hold");
        $this->assertEquals(2, sizeOf($this->players));
        $this->assertEquals("bank", $state);
        for ($i = 0; $i < 4; $i++) {
            [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "bank");
        }
        $this->assertGreaterThan($this->players[1]->getScore(), $this->players[0]->getScore());
        $this->assertEquals("Bank wins", $state);
        [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "bank");
        $this->assertEquals("Player wins", $state);
    }

    public function testPlayerHigherScore(): void
    {
        [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "first");
        for ($i = 0; $i < 5; $i++) {
            [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "draw");
        }
        [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "hold");
        for ($i = 0; $i < 4; $i++) {
            [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "bank");
        }
        $this->assertGreaterThan($this->players[0]->getScore(), $this->players[1]->getScore());
        $this->assertEquals("Player wins", $state);
    }
    //Testa samma poäng!!!

    public function testSameScore(): void
    {
        [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "first");
        [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "hold");
        // Just set the scores, don't need to test draw anymore. 
        // Will need to draw.
        $this->players[1]->setScore(20);
        for ($i = 0; $i < 4; $i++) {
            [$this->players, $state] = $this->gameLogic->play($this->players, $this->deck, "bank");
        }
        $this->assertEquals($this->players[0]->getScore(), $this->players[1]->getScore());
        $this->assertEquals("Bank wins", $state);
    }

}
