<?php

namespace App\Repository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\GameRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Game;

/**
 * Test cases for repository class GameRepository.
 */
class GameRepositoryTest extends KernelTestCase
{
    private ManagerRegistry $managerRegistry;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->managerRegistry = self::getContainer()->get(ManagerRegistry::class);
    }

    public function testGetGamesByNoPlayer(): void
    {
        $entityManager = $this->managerRegistry->getManager();
        $gameRepository = $entityManager->getRepository(Game::class);
        $this->assertInstanceOf(GameRepository::class, $gameRepository);
        $games = $gameRepository->getGamesByPlayer(818181881);
        $this->assertEmpty($games);
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
