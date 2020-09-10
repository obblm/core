<?php

namespace Obblm\Core\Repository;

use Obblm\Core\Entity\ChampionshipInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChampionshipInvitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChampionshipInvitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChampionshipInvitation[]    findAll()
 * @method ChampionshipInvitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChampionshipInvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChampionshipInvitation::class);
    }

    // /**
    //  * @return ChampionshipInvitation[] Returns an array of ChampionshipInvitation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChampionshipInvitation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
