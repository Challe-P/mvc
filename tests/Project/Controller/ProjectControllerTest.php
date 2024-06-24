<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Project\PokerLogic;
use Symfony\Component\BrowserKit\Cookie;

class ProjectControllerTest extends WebTestCase
{
    public function testProjectStart(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h2', "Welcome");
    }

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
        $client->request('GET', '/proj/play', $params);
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
        $client->request('GET', '/proj/play', $params);
        $client->request('GET', '/proj/play', $params);
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h3', "Total:");
        $client->request('GET', '/proj/api/delete/Test');
    }

    public function testProjectPlayNoSession(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj/play');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h2', "Start new game?");
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
        // Kontrollera att elementet existerar
        $this->assertCount(1, $input);
        // Kontrollera placeholder-attributet
        $placeholder = $input->attr('placeholder');
        $this->assertIsString($placeholder);
        $this->assertStringContainsString('Test', $placeholder);
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
        echo $response;
        $this->assertAnySelectorTextContains('h3', "Game finished");
    }

    public function testMusicPlayer(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj/music');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h2', "Music player");
    }

    public function testAbout(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj/about');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h2', "About");
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
