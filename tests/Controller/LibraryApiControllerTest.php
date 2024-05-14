<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class LibraryApiControllerTest extends WebTestCase
{
    public function testFindAll(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/library/books');
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function testFindNull(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/library/book/123123');
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
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
