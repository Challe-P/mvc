<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Project\PokerLogic;
use Symfony\Component\BrowserKit\Cookie;

class ProjectControllerTest extends WebTestCase
{
    public function testProjectPlay(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj');
        $session = $client->getRequest()->getSession();
        $session->set('name', "Test");
        $session->set('game', new PokerLogic());
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        $client->request('GET', '/proj/play');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h3', "Total:");
        $client->request('GET', '/proj/api/delete/Test');
    }

    public function testProjectPlayCard(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj');
        $session = $client->getRequest()->getSession();
        $session->set('name', "Test");
        $session->set('game', new PokerLogic());
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        $params = ['row' => 1, 'column' => 1];
        $client->request('POST', '/proj/play/resolve', $params);
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h3', "Total:");
        $client->request('GET', '/proj/api/delete/Test');
    }

    public function testProjectPlayCardPositionFilled(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj');
        $session = $client->getRequest()->getSession();
        $session->set('name', "Test");
        $session->set('game', new PokerLogic());
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        $params = ['row' => 1, 'column' => 1];
        $client->request('POST', '/proj/play/resolve', $params);
        $client->request('POST', '/proj/play/resolve', $params);
        $response = $client->getResponse();
        $session = $client->getRequest()->getSession();
        $game = $session->get('game');
        $this->assertInstanceOf(PokerLogic::class, $game);
        $this->assertEquals(51, count($game->deck->getCards()));
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h3', "Total:");
        $client->request('GET', '/proj/api/delete/Test');
    }

    public function testSetNameBet(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $params = ['name' => 'Test', 'bet' => 12];
        $client->request('POST', '/proj/set_namebet', $params);
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h3', "Total");
        $client->request('GET', '/proj/api/delete/Test');
    }

    public function testSetNameBetWithSession(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj');
        $session = $client->getRequest()->getSession();
        $session->set('name', "Test");
        $session->set('game', new PokerLogic());
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        $client->request('GET', '/proj/set_namebet');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h2', "Start new game?");
        $crawler = $client->getCrawler();
        $input = $crawler->filter('input[name="name"]')->first();
        // Assert that the element exists
        $this->assertNotEmpty($input);
        // Assert that the placeholderattribute contains the player name.
        $placeholder = $input->attr('placeholder');
        $this->assertIsString($placeholder);
        $this->assertStringContainsString('Test', $placeholder);
        // Remove the player from the database.
        $client->request('GET', '/proj/api/delete/Test');
    }

    public function testAutofill(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj');
        $session = $client->getRequest()->getSession();
        $session->set('name', "Test");
        $session->set('game', new PokerLogic());
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        $client->request('POST', '/proj/autofill');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h3', "Game finished");
    }

    public function testPlayResolveNoSession(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('POST', '/proj/play/resolve');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h2', "Start new game?");
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
