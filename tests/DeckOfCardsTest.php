<?php

namespace App\Game;

use App\Game\Exceptions\EmptyDeckException;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

/**
 * Test cases for class CardHand.
 */
class DeckOfCardsTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */

    public function testCreateDeckOfCards(): void
    {
        $deck = new DeckOfCards();
        $this->assertInstanceOf(DeckOfCards::class, $deck);
    }

    public function testPrintAll(): void
    {
        $deck = new DeckOfCards();
        // This looks dumb, but that's the way it is sometimes.
        $this->assertEquals("ace of hearts\n2 of hearts\n3 of hearts\n4 of hearts\n5 of hearts
6 of hearts\n7 of hearts\n8 of hearts\n9 of hearts\n10 of hearts\njack of hearts\nqueen of hearts
king of hearts\nace of spades\n2 of spades\n3 of spades\n4 of spades\n5 of spades\n6 of spades
7 of spades\n8 of spades\n9 of spades\n10 of spades\njack of spades\nqueen of spades
king of spades\nace of diamonds\n2 of diamonds\n3 of diamonds\n4 of diamonds\n5 of diamonds
6 of diamonds\n7 of diamonds\n8 of diamonds\n9 of diamonds\n10 of diamonds\njack of diamonds
queen of diamonds\nking of diamonds\nace of clubs\n2 of clubs\n3 of clubs\n4 of clubs\n5 of clubs
6 of clubs\n7 of clubs\n8 of clubs\n9 of clubs\n10 of clubs\njack of clubs\nqueen of clubs
king of clubs", $deck->printAll());
    }
    
    public function testGetCards(): void
    {
        $deck = new DeckOfCards();
        $this->assertEquals(52, sizeof($deck->getCards()));
    }

    public function testShuffle(): void
    {
        // Sets a seed for the randomizer
        srand(1);
        $deck = new DeckOfCards();
        $deck->shuffle();
        $this->assertEquals("jack of clubs", (string) $deck->getCards()[0]);
    }

    public function testDrawCard(): void
    {
        $deck = new DeckOfCards();
        $this->assertEquals("ace of hearts", $deck->drawCard());
        $this->assertEquals(51, $deck->cardsLeft());
        for ($i = 0; $i < 51; $i++) {
            $deck->drawCard();
        }
        $this->expectException(EmptyDeckException::class);
        $deck->drawCard();
    }

    public function testShuffleDrawn(): void
    {
        $deck = new DeckOfCards();
        $cardHand = new CardHand(5, $deck);
        $player = new Player("Testguy", $cardHand);
        $deck->shuffleDrawn([$player]);
        $this->assertEquals(47, $deck->cardsLeft());
        foreach ($player->getHand()->getHand() as $card) {
            $this->assertNotContains($card, $deck->getCards());
        }
    }

    public function testDrawCards(): void {
        $deck = new DeckOfCards();
        $cards = $deck->drawCards(10);
        $this->assertEquals(10, sizeof($cards));
    }

    public function testFromString(): void
    {
        $deck = new DeckOfCards();
        $deckString = $deck->printAll();
        $stringDeck = new DeckOfCards($deckString);
        $this->assertEquals($deckString, $stringDeck->printAll());
    }

    public function testFromStringDrawnCards(): void 
    {
        $deck = new DeckOfCards();
        $deck->drawCards(14);
        $this->assertEquals(38, $deck->cardsLeft());
        $deckString = $deck->printAll();
        $stringDeck = new DeckOfCards($deckString);
        $this->assertEquals($deckString, $stringDeck->printAll());
    }
}
