<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class GameApiControllerTest extends WebTestCase
{
    /**
     * @var array<array<string>> An array holding strings, to make a fake json for tests.
     */
    private array $fakeJson = [
        
        "deck" => [
            "ace of hearts",
            "2 of hearts",
            "3 of hearts",
            "4 of hearts",
            "5 of hearts",
            "6 of hearts",
            "7 of hearts",
            "8 of hearts",
            "9 of hearts",
            "10 of hearts",
            "jack of hearts",
            "queen of hearts",
            "king of hearts",
            "ace of spades",
            "2 of spades",
            "3 of spades",
            "4 of spades",
            "5 of spades",
            "6 of spades",
            "7 of spades",
            "8 of spades",
            "9 of spades",
            "10 of spades",
            "jack of spades",
            "queen of spades",
            "king of spades",
            "ace of diamonds",
            "2 of diamonds",
            "3 of diamonds",
            "4 of diamonds",
            "5 of diamonds",
            "6 of diamonds",
            "7 of diamonds",
            "8 of diamonds",
            "9 of diamonds",
            "10 of diamonds",
            "jack of diamonds",
            "queen of diamonds",
            "king of diamonds",
            "ace of clubs",
            "2 of clubs",
            "3 of clubs",
            "4 of clubs",
            "5 of clubs",
            "6 of clubs",
            "7 of clubs",
            "8 of clubs",
            "9 of clubs",
            "10 of clubs",
            "jack of clubs",
            "queen of clubs",
            "king of clubs"
        ]
        ];

    public function testApiDeck(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/deck');
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $json = json_encode($this->fakeJson, JSON_PRETTY_PRINT);
        $this->assertEquals($json, $response->getContent());
        $this->assertResponseIsSuccessful();
    }

    public function testApiDeckShuffleApi(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/deck/shuffle');
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
    }

    public function testApiDeckDraw(): void
    {
        $client = static::createClient();
        $request = ['amount' => (int) 1];
        $client->request('POST', '/api/deck/draw', $request);
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
    }

    public function testApiDeckDeal(): void
    {
        $client = static::createClient();
        $request = [
            'cards' => 3,
            'players' => 3
        ];
        $client->request('POST', '/api/deck/deal', $request);
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
    }

    public function testApiDeckDealTooMany(): void
    {
        $client = static::createClient();
        $request = [
            'cards' => 100,
            'players' => 3
        ];
        $client->request('POST', '/api/deck/deal', $request);
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
    }

    public function testApiDeckDealWrongInput(): void
    {
        $client = static::createClient();
        $request = [
            'cards' => "Hundra",
            'players' => "Tre"
        ];
        $client->request('POST', '/api/deck/deal', $request);
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
    }

    public function testApiDeckDrawWrongInput(): void
    {
        $client = static::createClient();
        $request = [
            'amount' => "Hundra"
        ];
        $client->request('POST', '/api/deck/draw', $request);
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
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
