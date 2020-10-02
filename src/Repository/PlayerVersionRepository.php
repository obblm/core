<?php

namespace Obblm\Core\Repository;

use Obblm\Core\Entity\PlayerVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlayerVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerVersion[]    findAll()
 * @method PlayerVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerVersion::class);
    }
}
