<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReportControllerTest extends WebTestCase
{
    public function testHome(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h1', "Vilhelm Malmberg Eskilsson");
    }

    public function testAbout(): void
    {
        $client = static::createClient();
        $client->request('GET', '/about');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h1', "Om kursen");
    }
    
    public function testReport(): void
    {
        $client = static::createClient();
        $client->request('GET', '/report');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h1', "Report");
    }

    public function testApi(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h1', "Den här sidan har de här APIerna:");
    }

    public function testApiQuote(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/quote');
        $response = $client->getResponse();
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testLucky(): void
    {
        $client = static::createClient();
        $client->request('GET', '/lucky');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h1', "Ditt själsdjur är:");
    }

    public function testMetrics(): void
    {
        $client = static::createClient();
        $client->request('GET', '/metrics');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertAnySelectorTextContains('h1', "Introduktion");
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
