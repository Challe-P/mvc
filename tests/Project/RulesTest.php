<?php

namespace App\Project;

use PHPUnit\Framework\TestCase;
use App\Game\Card;

/**
 * Test cases for class Project\Rules.
 */
class ProjectRulesTest extends TestCase
{
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

    
}