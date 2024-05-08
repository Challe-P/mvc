<?php

namespace App\Game\Exceptions;

use PHPUnit\Framework\TestCase;

/**
 * Test cases for exception classes.
 */
class ExceptionsTest extends TestCase
{
    /**
     * Construct object and verify that the object has the expected
     * properties, use no arguments.
     */
    public function testCreateEmptyDeckException(): void
    {
        $emptyDeckException = new EmptyDeckException();
        $this->assertInstanceOf(EmptyDeckException::class, $emptyDeckException);
        $this->assertEquals("Kortleken är slut.", $emptyDeckException->getMessage());
    }
}
