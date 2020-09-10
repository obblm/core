<?php

namespace Obblm\Core\Repository;

use Obblm\Core\Entity\EncounterAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EncounterAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method EncounterAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method EncounterAction[]    findAll()
 * @method EncounterAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EncounterActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EncounterAction::class);
    }

    // /**
    //  * @return EncounterAction[] Returns an array of EncounterAction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EncounterAction
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
