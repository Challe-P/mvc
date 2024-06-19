<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\BrowserKit\Cookie;
use App\Project\PokerLogic;
use App\Entity\Player;

class ProjectDatabaseControllerTest extends WebTestCase
{
    public function testUpdateWrongSession(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj');
        $session = $client->getRequest()->getSession();
        $session->set('name', "Test");
        $session->set('game', "Hej");
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        $client->request('GET', '/proj/update');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h2', "Start new game?");
        $client->request('GET', '/proj/api/delete/Test');
    }
    
    public function testUpdateFinishedWin(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj');
        // First game, to test triple winnings.
        $logic = new PokerLogic("", "", 10);
        $logic->finished = True;
        $logic->mat->setScore([10, 80]);
        $session = $client->getRequest()->getSession();
        $session->set('name', "Test");
        $session->set('game', $logic);
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        $client->request('GET', '/proj/update');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $session = $client->getRequest()->getSession();
        $player = $session->get('player');
        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals(60, $player->getBalance());
        // Second Game, to test normal winnings.
        $logic = new PokerLogic("", "", 10);
        $logic->mat->setScore([320, 80]);
        $logic->finished = True;
        $session->set('game', $logic);
        $session->set('gameEntry', null);
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        $client->request('GET', '/proj/update');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $session = $client->getRequest()->getSession();
        $player = $session->get('player');
        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals(80, $player->getBalance());
        $client->request('GET', '/proj/api/delete/Test');
    }

    public function testHighscore(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj/highscore');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h1', "Highscores!");
    }

    public function testPlayerPage(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        // Create a new game
        $params = [
            'name' => "Test",
            'bet' => 23
        ];
        $client->request('POST', '/proj/api/new', $params);
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        // Assert bet and spelare are in JSON
        $this->assertStringContainsString("\"name\": \"Test\"", $response);
        $this->assertStringContainsString("\"bet\": 23", $response);
        // Go to players site
        $client->request('GET', '/proj/player/Test');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h1', "Test's page!");
        $client->request('GET', '/proj/api/delete/Test');
    }

    public function testLoad(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('POST', '/proj/load', ['id' => 18]);
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h3', "Total:");
    }

    public function testLoadFail(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('POST', '/proj/load', ['id' => "Arton"]);
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h1', "Highscores!");
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
