<?php

namespace App\Project;

use PHPUnit\Framework\TestCase;

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
        $mat = $logic->autofill();
        // Check that none of the places are null
        for ($i = 0; $i < 5; $i++)
        {
            $this->assertNotContains(null, $mat->getHorizontalRows()[$i]->getRow());
            $this->assertNotContains(null, $mat->getVerticalRows()[$i]->getRow());
        }
    }
}