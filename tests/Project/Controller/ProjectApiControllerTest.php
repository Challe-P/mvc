<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Project\PokerLogic;
use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ProjectApiControllerTest extends WebTestCase
{
    private int $gameId = 0;
    private KernelBrowser $client;

    public function setUp(): void
    {
        // create a game to be used in tests.
        $this->client = static::createClient();
        $params = [
            'name' => "Test",
            'bet' => 23
        ];
        $this->client->followRedirects();
        $this->client->request('POST', '/proj/api/new', $params);
        $session = $this->client->getRequest()->getSession();
        $gameEntry = $session->get('gameEntry');
        $this->assertInstanceOf(Game::class, $gameEntry);
        $id = $gameEntry->getId();
        $this->assertNotNull($id);
        $this->gameId = $id;
    }

    public function testApiLanding(): void
    {
        $this->client->request('GET', '/proj/api');
        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h2', "JSON Api:s");
        $session = $this->client->getRequest()->getSession();
        $session->set('name', "Test");
        $session->set('game', new PokerLogic());
        $session->save();
        $this->client->getCookieJar()->set(new \Symfony\Component\BrowserKit\Cookie($session->getName(), $session->getId()));
        $this->client->request('GET', '/proj/api');
        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $crawler = $this->client->getCrawler();
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
        $params = [
            'name' => "Test",
            'bet' => 23
        ];
        $this->client->followRedirects();
        $this->client->request('POST', '/proj/api/new', $params);
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        // asserta att bet och spelare stämmer
    }

    public function testHighscoreJson(): void
    {
        // Borde skapa några användare i databasen för att säkerställa att det finns innehåll
        $this->client->request('GET', '/proj/api/highscore');
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
        // asserta att Test finns
    }

    public function testPlayerJson(): void
    {
        // Borde skapa några användare i databasen för att säkerställa att det finns innehåll
        $this->client->request('GET', '/proj/api/player/Test');
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
    }

    public function testApiPlay(): void
    {
        $this->client->request('GET', '/proj/api/game/' . $this->gameId . '/1:1');
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
        // asserta att första i placement är fylld.
    }

    public function testApiPlayNonValid(): void
    {
        $this->client->request('GET', '/proj/api/game/99999999/1:1');
        $this->client->followRedirects();
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
        // asserta samma som i highscore
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
