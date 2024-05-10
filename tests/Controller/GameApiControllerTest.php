<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Controller\GameApiController;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(GameApiController::class)]
class GameApiControllerTest extends WebTestCase
{
    public function testApiDeck(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/deck');
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $fakeJson =  ['deck' => ['ace of hearts', 'two of hearts']];
        $json = json_encode($fakeJson);
        //echo var_dump($response);
        //echo $response->message;
        $this->assertResponseIsSuccessful();
        //$this->assertJsonFileEqualsJsonFile($response, $json);
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
