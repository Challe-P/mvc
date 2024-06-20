<?php

namespace App\Project;

use PHPUnit\Framework\TestCase;
use App\Game\Card;
use App\Project\Exceptions\PositionFilledException;

/**
 * Test cases for class PokerLogic.
 */
class PokerLogicTest extends TestCase
{
    public function testCreateMat(): void
    {
        $logic = new PokerLogic();
        $this->assertInstanceOf(PokerLogic::class, $logic);
    }

    public function testAutofill(): void
    {
        $logic = new PokerLogic();
        $logic->autofill();
        // Check that none of the places are null
        for ($i = 0; $i < 5; $i++)
        {
            $this->assertNotContains(null, $logic->mat->getHorizontalRows()[$i]->getRow());
            $this->assertNotContains(null, $logic->mat->getVerticalRows()[$i]->getRow());
        }
    }

    public function testScoring(): void
    {
        $logic = new PokerLogic();
        // Row 1
        $logic->mat->setCard(0, 0, new Card("ace", "spades"));
        $logic->mat->setCard(0, 1, new Card("ace", "diamonds"));
        $logic->mat->setCard(0, 2, new Card("3", "clubs"));
        $logic->mat->setCard(0, 3, new Card("king", "spades"));
        $logic->mat->setCard(0, 4, new Card("9", "diamonds"));
        // Row 2
        $logic->mat->setCard(1, 0, new Card("queen", "hearts"));
        $logic->mat->setCard(1, 1, new Card("3", "diamonds"));
        $logic->mat->setCard(1, 2, new Card("2", "diamonds"));
        $logic->mat->setCard(1, 3, new Card("king", "hearts"));
        $logic->mat->setCard(1, 4, new Card("9", "spades"));
        // Row 3
        $logic->mat->setCard(2, 0, new Card("2", "clubs"));
        $logic->mat->setCard(2, 1, new Card("queen", "clubs"));
        $logic->mat->setCard(2, 2, new Card("3", "clubs"));
        $logic->mat->setCard(2, 3, new Card("7", "clubs"));
        $logic->mat->setCard(2, 4, new Card("king", "clubs"));
        // Row 4
        $logic->mat->setCard(3, 0, new Card("jack", "hearts"));
        $logic->mat->setCard(3, 1, new Card("7", "spades"));
        $logic->mat->setCard(3, 2, new Card("4", "hearts"));
        $logic->mat->setCard(3, 3, new Card("2", "hearts"));
        $logic->mat->setCard(3, 4, new Card("queen", "spades"));
        // Row 5
        $logic->mat->setCard(4, 0, new Card("5", "diamonds"));
        $logic->mat->setCard(4, 1, new Card("ace", "clubs"));
        $logic->mat->setCard(4, 2, new Card("jack", "clubs"));
        $logic->mat->setCard(4, 3, new Card("6", "spades"));
        $logic->mat->setCard(4, 4, new Card("9", "hearts"));
        $americanScore = 0;
        $englishScore = 0;
        foreach ($logic->mat->getHorizontalRows() as $row)
        {
            $americanScore += $logic->rules->scoreRow($row)[0];
            $englishScore += $logic->rules->scoreRow($row)[1];
        }
        
        foreach ($logic->mat->getVerticalRows() as $row)
        {
            $americanScore += $logic->rules->scoreRow($row)[0];
            $englishScore += $logic->rules->scoreRow($row)[1];
        }
        $this->assertEquals(38, $americanScore);
        $this->assertEquals(15, $englishScore);
        $this->assertEquals([38, 15], $logic->checkScore());
    }

    public function testAutofillPositionFull(): void
    {
        $logic = new PokerLogic();
        $logic->setCard(1,1, new Card());
        $logic->autofill();
        for ($i = 0; $i < 5; $i++)
        {
            $this->assertNotContains(null, $logic->mat->getHorizontalRows()[$i]->getRow());
            $this->assertNotContains(null, $logic->mat->getVerticalRows()[$i]->getRow());
        }
    }

    public function testSetCardPositionFull(): void
    {
        $logic = new PokerLogic();
        $logic->setCard(1,1, new Card());
        $this->expectException(PositionFilledException::class);
        $logic->setCard(1,1, new Card());
    }
}
