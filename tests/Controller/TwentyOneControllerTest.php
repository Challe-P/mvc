<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TwentyOneControllerTest extends WebTestCase
{
    public function testGamePlay(): void
    {
        $client = static::createClient();
        $client->request('GET', '/game/play');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        // det här nedan ska fixas till och föras över till en "bank win"-test
        $client->submitForm('hold');
        $buttonExists = true;
        while ($buttonExists) {
            try {
                $client->submitForm('bank');
            } catch (\Exception $e) {
                $buttonExists = false;
            }
        }
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testGameDoc(): void
    {
        $client = static::createClient();
        $client->request('GET', '/game/doc');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testStart(): void
    {
        $client = static::createClient();
        $client->request('GET', "/game/");
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
    }

    /*
    public function testPlayerWin(): void
    {
        // Kanske kan mocka testet?
    }

    public function testShuffleDrawn(): void
    {
        // spela tills kortleken är slut? Går detta att mocka?
    }

    public function testNullPlayer(): void 
    {
        // måste nästan mocka en felaktig array?
    }
    */

    protected function restoreExceptionHandler(): void
    {
    while (true) {
        $previousHandler = set_exception_handler(static fn() => null);

        restore_exception_handler();

        if ($previousHandler === null) {
            break;
        }

        restore_exception_handler();
        }
    }

    protected function tearDown(): void
    {
    parent::tearDown();

    $this->restoreExceptionHandler();
    }

}
