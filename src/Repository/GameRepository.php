<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findGameById(?int $id): ?Game
    {
        $result = $this->createQueryBuilder('f')
            ->andWhere('f.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult();
        if ($result instanceof Game) {
            return $result;
        }
        return null;

    }

    /**
     * @return array<Game>
     */
    public function getGamesByPlayer(int $id): ?array
    {
        $result = $this->createQueryBuilder('f')
            ->andWhere('f.player_id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getResult();
        if (!is_array($result)) {
            return null;
        }
        foreach ($result as $element) {
            if (!$element instanceof Game) {
                return null;
            }
        }
        return $result;
    }

    /**
     * @return array<Game>
     */
    public function findAllSorted(): array
    {
        return $this->findBy(array(), array('americanScore' => 'DESC'));
    }
}
