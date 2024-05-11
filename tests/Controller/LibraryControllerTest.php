<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LibraryControllerTest extends WebTestCase
{
    public function testLibrary(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
        $this->assertAnySelectorTextContains('h1', 'Välkommen till biblioteket');
    }

    public function testShowBookById(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library/show/1');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
    }

    public function testCreateForm(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library/create_form');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
    }

    public function testUpdateBookById(): void
    {
        $client = static::createClient();
        $client->request('GET', '/library/update_form/1');
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
    }

    public function testCreateDelete(): void
    {
        $client = static::createClient();
        $params = [
            'title' => 'Tests and stuff',
            'firstname' => 'Testy',
            'surname' => 'McTestyFace',
            'isbn' => '97891351123',
            'image' => '/img/books/97891351123.jpg'
        ];

        // Add book.
        $client->request('POST', '/library/book_create', $params);
        $client->followRedirect();
        $response = $client->getResponse();
        $this->assertAnySelectorTextContains('h1', 'Välkommen till biblioteket');
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
        $this->assertAnySelectorTextContains('td', "Tests and stuff");

        // Find the book.
        $client->request('GET', "/api/library/book/" . $params['isbn']);
        $response = $client->getResponse();
        $this->assertNotFalse($response->getContent());
        $json = json_decode($response->getContent());
        $this->assertIsObject($json);
        $this->assertObjectHasProperty('id', $json);

        // Remove the book.
        // @phpstan-ignore-next-line
        $client->request('POST', "/library/delete", ['id' => $json->id]);
        $client->followRedirect();
        $client->getResponse();
        $this->assertAnySelectorTextNotContains('td', "Tests and stuff");
    }

    public function testDeleteNotFound(): void
    {
        $client = static::createClient();
        $client->request('POST', "/library/delete", ['id' => "gurka"]);
        $this->assertResponseStatusCodeSame(404, $client->getInternalResponse());
    }

    public function testUpdateBook(): void
    {
        $client = static::createClient();

        //Add a book
        $params = [
            'title' => 'New tests',
            'firstname' => 'Testy',
            'surname' => 'McTestyFace',
            'isbn' => '97891351123',
            'image' => '/img/books/97891351123.jpg'
        ];
        $client->request('POST', '/library/book_create', $params);
        $client->followRedirect();
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
        $this->assertAnySelectorTextContains('td', "New tests");
        $this->assertAnySelectorTextNotContains('td', "Bread is good");

        // Find the book.
        $client->request('GET', "/api/library/book/" . $params['isbn']);
        $response = $client->getResponse();
        $this->assertNotFalse($response->getContent());
        $json = json_decode($response->getContent());
        $this->assertIsObject($json);
        $this->assertObjectHasProperty('id', $json);

        //Update the book
        $newParams = [
        // @phpstan-ignore-next-line
            'id' => $json->id,
            'title' => "Bread is good",
            'firstname' => "Breadman",
            'surname' => "Breadson",
            'isbn' => "978913511277",
            'image' => 'bread.png'
        ];
        $client->request('POST', '/library/book_update', $newParams);
        $client->followRedirect();
        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertResponseIsSuccessful();
        $this->assertAnySelectorTextContains('h1', "Bread is good");
        $this->assertAnySelectorTextNotContains('h1', "New tests");
        
        
        //Remove the book
        // @phpstan-ignore-next-line
        $client->request('POST', "/library/delete", ['id' => $json->id]);
        $client->followRedirect();
        $client->getResponse();
        $this->assertAnySelectorTextNotContains('td', "Bread is good");
    }

    public function testUpdateBookNotFound(): void
    {
        $client = static::createClient();
        $client->request('POST', '/library/book_update', ['id' => "gröt"]);
        $client->followRedirect();
        $this->assertAnySelectorTextContains('div', "No book found");
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
