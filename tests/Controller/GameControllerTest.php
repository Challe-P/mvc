<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GameControllerTest extends WebTestCase
{
    public function testFirst(): void
    {
        $client = static::createClient();
        $client->request('GET', '/card');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h1', "Välkommen till kortspelsportalen!");
    }

    public function testDeck(): void
    {
        $client = static::createClient();
        $client->request('GET', '/card/deck');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h1', "Här är alla kort i korleken!");
    }
    
    public function testShuffle(): void
    {
        $client = static::createClient();
        $client->request('GET', '/card/deck/shuffle');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h1', "Här är din blandade kortlek!");
    }

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
