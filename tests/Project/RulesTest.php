<?php

namespace App\Project;

use PHPUnit\Framework\TestCase;
use App\Game\Card;
use App\Project\Exceptions\InvalidRowException;

/**
 * Test cases for class Project\Rules.
 */
class ProjectRulesTest extends TestCase
{
    // This needs to be split into the new classes
    public function testStraightNonRoyalNonFlush(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('2', 'spades'));
        $row->setCard(1, new Card('3', 'diamonds'));
        $row->setCard(2, new Card('4', 'hearts'));
        $row->setCard(3, new Card('5', 'clubs'));
        $row->setCard(4, new Card('6', 'spades'));
        $this->assertEquals([15, 12], $rules->scoreRow($row));
    }

    public function testStraightFlush(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('2', 'spades'));
        $row->setCard(1, new Card('3', 'spades'));
        $row->setCard(2, new Card('4', 'spades'));
        $row->setCard(3, new Card('5', 'spades'));
        $row->setCard(4, new Card('6', 'spades'));
        $this->assertEquals([75, 30], $rules->scoreRow($row));
    }

    public function testRoyalFlush(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('10', 'spades'));
        $row->setCard(1, new Card('jack', 'spades'));
        $row->setCard(2, new Card('queen', 'spades'));
        $row->setCard(3, new Card('king', 'spades'));
        $row->setCard(4, new Card('ace', 'spades'));
        $this->assertEquals([100, 30], $rules->scoreRow($row));
    }

    public function testFlush(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('3', 'spades'));
        $row->setCard(1, new Card('jack', 'spades'));
        $row->setCard(2, new Card('queen', 'spades'));
        $row->setCard(3, new Card('king', 'spades'));
        $row->setCard(4, new Card('6', 'spades'));
        $this->assertEquals([20, 5], $rules->scoreRow($row));
    }

    public function testFlushNoStraightWithAce(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('3', 'spades'));
        $row->setCard(1, new Card('jack', 'spades'));
        $row->setCard(2, new Card('queen', 'spades'));
        $row->setCard(3, new Card('ace', 'spades'));
        $row->setCard(4, new Card('6', 'spades'));
        $this->assertEquals([20, 5], $rules->scoreRow($row));
    }

    public function testStraightWithAce(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('2', 'clubs'));
        $row->setCard(1, new Card('4', 'spades'));
        $row->setCard(2, new Card('3', 'spades'));
        $row->setCard(3, new Card('ace', 'spades'));
        $row->setCard(4, new Card('5', 'spades'));
        $this->assertEquals([15, 12], $rules->scoreRow($row));
    }
    
    public function testFullHouse(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('2', 'clubs'));
        $row->setCard(1, new Card('2', 'spades'));
        $row->setCard(2, new Card('3', 'spades'));
        $row->setCard(3, new Card('3', 'clubs'));
        $row->setCard(4, new Card('3', 'diamonds'));
        $this->assertEquals([25, 10], $rules->scoreRow($row));
    }

    public function testFourOfAKind(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('2', 'clubs'));
        $row->setCard(1, new Card('3', 'hearts'));
        $row->setCard(2, new Card('3', 'spades'));
        $row->setCard(3, new Card('3', 'clubs'));
        $row->setCard(4, new Card('3', 'diamonds'));
        $this->assertEquals([50, 16], $rules->scoreRow($row));
    }

    public function testNothing(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('2', 'clubs'));
        $row->setCard(1, new Card('6', 'spades'));
        $row->setCard(2, new Card('jack', 'spades'));
        $row->setCard(3, new Card('10', 'clubs'));
        $row->setCard(4, new Card('3', 'diamonds'));
        $this->assertEquals([0, 0], $rules->scoreRow($row));
    }

    public function testUnfilledRow(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('2', 'clubs'));
        $this->assertEquals([0, 0], $rules->scoreRow($row));
    }

    public function testPair(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('2', 'clubs'));
        $row->setCard(1, new Card('2', 'diamonds'));
        $row->setCard(2, new Card('jack', 'spades'));
        $row->setCard(3, new Card('10', 'clubs'));
        $row->setCard(4, new Card('3', 'diamonds'));
        $this->assertEquals([2, 1], $rules->scoreRow($row));
    }

    public function testTwoPair(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('2', 'clubs'));
        $row->setCard(1, new Card('2', 'diamonds'));
        $row->setCard(2, new Card('3', 'spades'));
        $row->setCard(3, new Card('3', 'clubs'));
        $row->setCard(4, new Card('5', 'diamonds'));
        $this->assertEquals([5, 3], $rules->scoreRow($row));
    }

    public function testThreeOfAKind(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('2', 'clubs'));
        $row->setCard(1, new Card('2', 'diamonds'));
        $row->setCard(2, new Card('2', 'spades'));
        $row->setCard(3, new Card('3', 'clubs'));
        $row->setCard(4, new Card('5', 'diamonds'));
        $this->assertEquals([10, 6], $rules->scoreRow($row));
    }

    public function testInvalidRow(): void
    {
        $rules = new Rules();
        $row = new Row();
        $row->setCard(0, new Card('2', 'clubs'));
        $row->setCard(1, new Card('2', 'diamonds'));
        $row->setCard(2, new Card('2', 'spades'));
        $row->setCard(3, new Card('2', 'clubs'));
        $row->setCard(4, new Card('2', 'diamonds'));
        $this->expectException(InvalidRowException::class);
        $rules->scoreRow($row);
    }
}
