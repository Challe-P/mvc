<?php

namespace App\Project;

use PHPUnit\Framework\TestCase;
use App\Game\Card;

/**
 * Test cases for class Mat.
 */
class MatTest extends TestCase
{
    public function testCreateMat(): void
    {
        $mat = new Mat();
        $this->assertInstanceOf(Mat::class, $mat);
    }

    public function testPrintEmpty(): void
    {
        $mat = new Mat();
        // How do I get this to look better?
        $matString = "null, null, null, null, null\n";
        $matString .= "null, null, null, null, null\n";
        $matString .= "null, null, null, null, null\n";
        $matString .= "null, null, null, null, null\n";
        $matString .= "null, null, null, null, null";
        $this->assertEquals($matString, (string) $mat);
    }

    public function testSetCard(): void
    {
        $mat = new Mat();
        $card = new Card();
        $mat->setCard(2, 1, $card);
        $matString = "null, null, null, null, null\n";
        $matString .= "null, null, null, null, null\n";
        $matString .= "null, ace of spades, null, null, null\n";
        $matString .= "null, null, null, null, null\n";
        $matString .= "null, null, null, null, null";
        $this->assertEquals($matString, (string) $mat);
        $this->assertEquals($card, $mat->getHorizontalRows()[2]->getRow()[1]);
        $this->assertEquals($card, $mat->getVerticalRows()[1]->getRow()[2]);
    }
}