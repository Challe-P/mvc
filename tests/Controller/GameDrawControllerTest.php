<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GameDrawControllerTest extends WebTestCase
{
    public function testDraw(): void
    {
        $client = static::createClient();
        $client->request('GET', '/card/deck/draw');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);

        for ($i = 0; $i < 52; $i++) {
            $client->request('GET', '/card/deck/draw');
        }
        $client->getResponse();
        $this->assertAnySelectorTextContains('div', "There's not enough cards left.");
    }
    
    public function testDrawAmount(): void
    {
        $client = static::createClient();
        $client->request('GET', '/card/deck/draw/:5');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
    }

    public function testDrawTooMany(): void
    {
        $client = static::createClient();
        $client->request('GET', '/card/deck/draw/:100');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
        $this->assertAnySelectorTextContains('div', "There's not enough cards left.");
    }

    public function testDeal(): void
    {
        $client = static::createClient();
        $client->request('GET', '/card/deck/deal/:5/:5');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
    }

    public function testDealTooMany(): void
    {
        $client = static::createClient();
        $client->request('GET', '/card/deck/deal/:100/:100');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
        $this->assertAnySelectorTextContains('div', "There's not enough cards left.");
    }

    public function testShuffleDeckToDraw(): void
    {
        $client = static::createClient();
        $client->request('GET', '/card/deck/draw/shuffle');
        $client->followRedirect();
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
    }

    public function testDrawAmountPost(): void
    {
        $client = static::createClient();
        $client->request('POST', '/card/deck/draw/:', ['amount' => 5]);
        $client->followRedirect();
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
    }

    public function testDealPost(): void
    {
        $client = static::createClient();
        $client->request('POST', '/card/deck/deal/', ['players' => 5, 'cards' => 2]);
        $client->followRedirect();
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
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
