<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Game\Player;
use App\Game\DeckOfCards;
use App\Game\Card;

class TwentyOneControllerTest extends WebTestCase
{
    public function testGamePlay(): void
    {
        
        $client = static::createClient();
        $client->request('GET', '/game/play');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
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

    public function testPlayerWin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/game/play');
        $session = $client->getRequest()->getSession();
        $client->request('POST', '/game/play', ["state" => "hold"]);
        $players = $session->get('players');
        $this->assertIsArray($players);
        $this->assertInstanceOf(Player::class, $players[0]);
        $players[0]->getHand()->removeCard(0);
        $players[0]->getHand()->addCard(new Card("king", "spades"));
        $players[0]->getHand()->addCard(new Card("king", "spades"));
        $session->set('players', $players);
        $session->save();
        $client->request('POST', '/game/play', ["state" => "bank"]);
        $this->assertAnySelectorTextContains('div', "Du vann!");
    }
    
    public function testBankWin(): void
    {
        // Jobba runt problemet med att ett kort dras genom att skapa en ny deck
        $client = static::createClient();
        $client->request('GET', '/game/play');
        $session = $client->getRequest()->getSession();
        $players = $session->get('players');
        $this->assertIsArray($players);
        $this->assertInstanceOf(Player::class, $players[0]);
        $players[0]->getHand()->removeCard(0);
        $players[0]->getHand()->addCard(new Card("king", "spades"));
        $players[0]->getHand()->addCard(new Card("king", "spades"));
        $session->set('players', $players);
        $session->save();
        $client->request('POST', '/game/play', ["state" => "draw"]);
        $session = $client->getRequest()->getSession();
        $this->assertAnySelectorTextContains('div', "Banken vann!");
    }
    
    public function testDeckAlmostEmpty(): void
    {
        $client = static::createClient();
        $client->request('GET', '/game/play');
        $session = $client->getRequest()->getSession();
        $deck = $session->get('deck');
        $this->assertInstanceOf(DeckOfCards::class, $deck);
        $deck->drawCards(51);
        $session->set('deck', $deck);
        $session->save();
        $client->request('POST', '/game/play', ["state" => "draw"]);
        $session = $client->getRequest()->getSession();
        $this->assertAnySelectorTextContains('h4', "Bankens hand");
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
