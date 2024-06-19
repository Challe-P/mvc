<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class ProjectApiControllerPlayTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void 
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
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
