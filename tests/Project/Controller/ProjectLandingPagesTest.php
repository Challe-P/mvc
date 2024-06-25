<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Project\PokerLogic;
use Symfony\Component\BrowserKit\Cookie;

/**
 * Tests for easy landing pages, just to make certain they work.
 */
class ProjectLandingPagesTest extends WebTestCase
{
    public function testProjectStart(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h2', "Welcome");
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

    public function testApiLanding(): void
    {
        $client = static::createClient();
        $client->followRedirects();
        $client->request('GET', '/proj/api');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h2', "JSON Api:s");
        $session = $client->getRequest()->getSession();
        $session->set('name', "Test");
        $session->set('game', new PokerLogic());
        $session->save();
        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
        $client->request('GET', '/proj/api');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $crawler = $client->getCrawler();
        $input = $crawler->filter('input[name="name"]')->first();
        // Kontrollera att elementet existerar
        $this->assertCount(1, $input);
        // Kontrollera placeholder-attributet
        $placeholder = $input->attr('placeholder');
        $this->assertIsString($placeholder);
        $this->assertStringContainsString('Test', $placeholder);
    }

    public function testHighscore(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj/highscore');
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
