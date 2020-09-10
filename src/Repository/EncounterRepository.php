<?php

namespace Obblm\Core\Repository;

use Obblm\Core\Entity\Encounter;
use Obblm\Core\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Encounter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Encounter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Encounter[]    findAll()
 * @method Encounter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EncounterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Encounter::class);
    }

    public function getOpenedEncounter(Team $team) {
        $qb = $this->createQueryBuilder('e');
        return $qb
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->eq('e.home_team', ':team'),
                    $qb->expr()->eq('e.visitor_team', ':team')
                )
            )
            ->andWhere('e.validated_at IS NULL')
            ->setParameter(':team', $team)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
}
