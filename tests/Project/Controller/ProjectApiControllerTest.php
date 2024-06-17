<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Project\PokerLogic;
use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;

class ProjectApiControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void 
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
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
        $this->client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
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
        $this->client->request('POST', '/proj/api/new', $params);
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        // Asserta att bet och spelare finns i JSON-strängen
        $this->assertStringContainsString("\"name\": \"Test\"", $response);
        $this->assertStringContainsString("\"bet\": 23", $response);
        $this->client->request('GET', '/proj/api/delete/Test');
    }

    public function testHighscoreJson(): void
    {
        $params = [
            'name' => "Test",
            'bet' => 23
        ];
        $this->client->request('POST', '/proj/api/new', $params);
        $this->client->request('GET', '/proj/api/highscore');
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
        // Asserta att Test finns i listan
        $this->assertStringContainsString("\"name\": \"Test\"", $response);
        $this->client->request('GET', '/proj/api/delete/Delety');
    }

    public function testPlayerJson(): void
    {
        $params = [
            'name' => "Test",
            'bet' => 23
        ];
        $this->client->request('POST', '/proj/api/new', $params);
        $this->client->request('GET', '/proj/api/player/Test');
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString("\"name\": \"Test\"", $response);
        $this->assertStringContainsString("\"bet\": 23", $response);
        $this->client->request('GET', '/proj/api/delete/Test');
    }

    public function testApiPlay(): void
    {
        $id = $this->createGame();
        $this->client->request('GET', '/proj/api/game/' . $id . '/1:1');
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
        // Asserta att första i placement är fylld.
        $this->assertStringNotContainsString("\"placement\": null", $response);
        $this->client->request('GET', '/proj/api/delete/Test');
    }

    public function testApiPlayNonValid(): void
    {
        $this->client->request('GET', '/proj/api/game/99999999/1:1');
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
        // Asserta samma som i highscore, eftersom den reroutas dit
        $this->assertStringContainsString("\"Players\":", $response);
    }

    public function testApiPostPlay(): void
    {
        $id = $this->createGame();
        $params = ['id' => $id,
        'row' => 1,
        'column' => 3];
        $this->client->request('POST', '/proj/api/gamepost', $params);
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString("\"name\": \"Test\"", $response);
        // Asserta att rad 1 inte är tom.
        $this->assertStringNotContainsString("\"placement\": \"null, null, null, null, null", $response);
        $this->client->request('GET', '/proj/api/delete/Test');
    }

    public function testApiPostPlayNoGameInt(): void
    {
        $params = ['id' => "Korv",
        'row' => 2,
        'column' => 2];
        $this->client->request('POST', '/proj/api/gamepost', $params);
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
        // Asserta samma som i highscore, eftersom den reroutas dit
        $this->assertStringContainsString("\"Players\":", $response);
    }

    public function testApiPostPlayNoGameExists(): void
    {
        $params = ['id' => 99999999999,
        'row' => 2,
        'column' => 2];
        $this->client->request('POST', '/proj/api/gamepost', $params);
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
        // Asserta samma som i highscore, eftersom den reroutas dit
        $this->assertStringContainsString("\"Players\":", $response);
    }

    public function testApiPostPlayPositionFilled(): void
    {
        $id = $this->createGame();
        $params = ['id' => $id,
        'row' => 2,
        'column' => 2];
        $this->client->request('POST', '/proj/api/gamepost', $params);
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString("\"name\": \"Test\"", $response);
        $this->client->request('POST', '/proj/api/gamepost', $params);
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString("\"name\": \"Test\"", $response);
        $this->client->request('GET', '/proj/api/delete/Test');
    }

    public function testApiShowGameNotNumeric(): void
    {
        $this->client->request('GET', 'proj/api/game/hans');
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertStringContainsString("\"Players\":", $response);
    }

    public function testDeletePlayer(): void 
    {
        $params = [
            'name' => "Delety",
            'bet' => 20
        ];
        $this->client->request('POST', '/proj/api/new', $params);
        $response = $this->client->getResponse();
        $this->assertStringContainsString("\"name\": \"Delety\"", $response);
        $this->client->request('GET', '/proj/api/delete/Delety');
        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertStringNotContainsString("\"name\": \"Delety\"", $response);
    }

    private function createGame(): int 
    {
        $params = [
            'name' => "Test",
            'bet' => 23
        ];
        $this->client->request('POST', '/proj/api/new', $params);
        $session = $this->client->getRequest()->getSession();
        $gameEntry = $session->get('gameEntry');
        $this->assertInstanceOf(Game::class, $gameEntry);
        $id = $gameEntry->getId();
        $this->assertNotNull($id);
        return $id;
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
