<?php

namespace App\Project;

use PHPUnit\Framework\TestCase;
use App\Game\Card;

/**
 * Test cases for class Project\Rules.
 */
class UniqueValuesRulesTest extends TestCase
{
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
}
