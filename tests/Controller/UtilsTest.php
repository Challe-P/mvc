<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Controller\Utils;

class UtilsTest extends WebTestCase
{
    // Tests a path that doesn't happen, but is a fallback.
    public function testNullPlayerArray(): void
    {
        $utils = new Utils;
        $client = static::createClient();
        $client->request('GET', '/game/play');
        $session = $client->getRequest()->getSession();
        $players = $session->get('players');
        $this->assertIsArray($players);
        $players[0] = [100];
        $session->set('players', $players);
        $session->save();
        $this->assertEquals(null, $utils->playerCheck($session));
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
