<?php

namespace App\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Game;

/**
 * Test cases for entity class Game.
 */
class GameEntityTest extends TestCase
{
    public function testSetType(): void
    {
        $game = new Game();
        $game->setType("Normal");
        $this->assertEquals("Normal", $game->getType());
    }
}
