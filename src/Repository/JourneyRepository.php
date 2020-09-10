<?php

namespace Obblm\Core\Repository;

use Obblm\Core\Entity\Journey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Journey|null find($id, $lockMode = null, $lockVersion = null)
 * @method Journey|null findOneBy(array $criteria, array $orderBy = null)
 * @method Journey[]    findAll()
 * @method Journey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JourneyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Journey::class);
    }
}
