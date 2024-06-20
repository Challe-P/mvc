<?php

namespace App\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Player;

/**
 * Test cases for entity class Player.
 */
class PlayerEntityTest extends TestCase
{
    public function testSetId(): void
    {
        $player = new Player();
        $player->setId(614);
        $this->assertEquals(614, $player->getId());
    }
}
