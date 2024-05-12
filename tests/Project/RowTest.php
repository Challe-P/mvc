<?php

namespace App\Project;

use PHPUnit\Framework\TestCase;
use App\Game\Card;
use App\Project\Exceptions\PositionFilledException;

/**
 * Test cases for class Row.
 */
class RowTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateRow(): void
    {
        $row = new Row();
        $this->assertInstanceOf(Row::class, $row);
        $this->assertIsArray($row->getRow());
    }
    
    public function testSetCard(): void
    {
        $row = new Row();
        $card = new Card();
        $secondCard = new Card("jack", "diamonds");
        $row->setCard(0, $card);
        $row->setCard(4, $secondCard);
        $this->assertEquals($row->getRow()[0], $card);
        $this->assertEquals($row->getRow()[4], $secondCard);
    }

    public function testSetCardFull(): void {
        $row = new Row();
        $card = new Card();
        $row->setCard(4, $card);
        $this->assertEquals($row->getRow()[4], $card);
        $this->expectException(PositionFilledException::class);
        $row->setCard(4, new Card());
    }

    public function testToString(): void {
        $row = new Row();
        $card = new Card();
        $secondCard = new Card("jack", "diamonds");
        $row->setCard(0, $card);
        $row->setCard(4, $secondCard);
        $this->assertEquals("ace of spades, null, null, null, jack of diamonds", (string) $row);
    }

    public function testIsFilled(): void {
        $row = new Row();
        $this->assertFalse($row->isFilled());
        $card = new Card();
        $row->setCard(0, $card);
        $row->setCard(1, $card);
        $row->setCard(2, $card);
        $row->setCard(3, $card);
        $row->setCard(4, $card);
        $this->assertTrue($row->isFilled());
    }
}
