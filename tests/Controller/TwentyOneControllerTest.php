<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TwentyOneControllerTest extends WebTestCase
{
    public function testGamePlay(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/game/play');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $buttonExists = true;
        while ($buttonExists) {
            try {
                $client->submitForm('bank');
            } catch (\Exception $e) {
                $buttonExists = false;
            }
        }
        $response = $client->getResponse();
    }

    public function testGameDoc(): void
    {
        $client = static::createClient();
        $client->request('GET', '/game/doc');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
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
