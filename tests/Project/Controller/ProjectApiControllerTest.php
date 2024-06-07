<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Project\PokerLogic;

class ProjectApiControllerTest extends WebTestCase
{
    public function setUp(): void
    {
        // create a game to be used in tests.
    }

    public function testApiLanding(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj/api');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h2', "JSON Api:s");
        $session = $client->getRequest()->getSession();
        $session->set('name', "Test");
        $session->set('game', new PokerLogic());
        $session->save();
        $client->getCookieJar()->set(new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId()));
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

    public function testNewGameApi(): void
    {
        $client = static::createClient();
        $params = [
            'name' => "Test",
            'bet' => 23
        ];
        $client->followRedirects();
        $client->request('POST', '/proj/api/new', $params);
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        // asserta att bet och spelare stämmer
    }

    public function testHighscoreJson(): void
    {
        // Borde skapa några användare i databasen för att säkerställa att det finns innehåll
        $client = static::createClient();
        $client->request('GET', '/proj/api/highscore');
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
        // asserta att Test finns
    }

    public function testPlayerJson(): void
    {
        // Borde skapa några användare i databasen för att säkerställa att det finns innehåll
        $client = static::createClient();
        $client->request('GET', '/proj/api/player/Test');
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
    }
/*
    public function testApiPlay(): void
    {
        $client = static::createClient();
        $client->request('GET', '/proj/api/game/{id}/{row}:{column}');
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
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
