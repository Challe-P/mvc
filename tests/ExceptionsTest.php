<?php

namespace App\Game\Exceptions;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Test cases for exception classes.
 */
#[CoversClass(EmptyDeckException::class)]
class ExceptionsTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateTarotDeck(): void
    {
        $emptyDeckException = new EmptyDeckException();
        $this->assertInstanceOf(EmptyDeckException::class, $emptyDeckException);
        $this->assertEquals("Kortleken är slut.", $emptyDeckException->getMessage());
    }
}
