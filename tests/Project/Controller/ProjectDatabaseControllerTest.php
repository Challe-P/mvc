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
        $client->request('GET', '/proj/play');
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
        // First game, to test normal winnings.
        $matString = "ace of spades, king of spades, queen of spades, jack of spades, 10 of spades\n";
        $matString .= "2 of hearts, 7 of hearts, 3 of hearts, 9 of hearts, 10 of hearts\n";
        $matString .= "2 of spades, 7 of spades, 3 of spades, 9 of spades, 10 of spades\n";
        $matString .= "2 of diamonds, 7 of diamonds, 3 of diamonds, 9 of diamonds, 9 of diamonds\n";
        $matString .= "ace of hearts, king of hearts, queen of hearts, jack of hearts, null";
        $logic = new PokerLogic("11 of diamonds\n11 of diamonds", $matString, 10);
        // Assert score is high enough
        $logic->checkScore();
        $this->assertGreaterThanOrEqual(200, $logic->mat->getScore()[0]);
        $this->assertGreaterThanOrEqual(70, $logic->mat->getScore()[1]);
        $this->assertLessThan(310, $logic->mat->score[0]);
        $this->assertLessThan(120, $logic->mat->score[1]);
        $session = $client->getRequest()->getSession();
        $session->set('name', "Test");
        $session->set('game', $logic);
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        $client->request('POST', '/proj/play/resolve', ['row' => 4, 'column' => 4]);
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $session = $client->getRequest()->getSession();
        $player = $session->get('player');
        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals(60, $player->getBalance());
        
        // Second Game, to test triple winnings.
        $matString = "ace of spades, king of spades, queen of spades, jack of spades, 10 of spades\n";
        $matString .= $matString . $matString . $matString;
        $matString .= "10 of hearts, jack of hearts, king of hearts, ace of hearts, null";
        $logic = new PokerLogic("queen of hearts\njack of hearts", $matString, 10);
        $logic->checkScore();
        $this->assertGreaterThanOrEqual(320, $logic->mat->getScore()[0]);
        $this->assertGreaterThanOrEqual(120, $logic->mat->getScore()[1]);
        $session->set('game', $logic);
        $session->set('gameEntry', null);
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        $client->request('POST', '/proj/play/resolve', ['row' => 4, 'column' => 4]);
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $session = $client->getRequest()->getSession();
        $player = $session->get('player');
        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals(80, $player->getBalance());
        $client->request('GET', '/proj/api/delete/Test');
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
        // Assert bet and player are in JSON
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
