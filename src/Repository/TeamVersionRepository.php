<?php

namespace Obblm\Core\Repository;

use Obblm\Core\Entity\Championship;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TeamVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeamVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeamVersion[]    findAll()
 * @method TeamVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamVersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamVersion::class);
    }
}
