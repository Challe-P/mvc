<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Player>
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    public function findPlayerByName(string $name): ?Player
    {
        $result = $this->createQueryBuilder('p')
                    ->andWhere('p.name = :val')
                    ->setParameter('val', $name)
                    ->getQuery()
                    ->getOneOrNullResult();
        if ($result instanceof Player) {
            return $result;
        }
        return null;
    }

    /**
     * @return array<Player>
     */
    public function findAllSorted(): array
    {
        return $this->findBy(array(), array('balance' => 'DESC'));
    }
}
