<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TwentyOneApiControllerTest extends WebTestCase
{
    public function testGameApiEmpty(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/game');
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function testGameApi(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/game');
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $client->request('GET', '/game/play');
        $client->getResponse();
        $buttonExists = true;
        while ($buttonExists) {
            try {
                $client->submitForm('bank');
            } catch (\Exception $e) {
                $buttonExists = false;
            }
        }
        $client->request('GET', '/api/game');
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function testStart(): void
    {
        $client = static::createClient();
        $client->request('GET', "/game/");
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
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
