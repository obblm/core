<?php

namespace Obblm\Core\Repository;

use Obblm\Core\Entity\Championship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @method Championship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Championship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Championship[]    findAll()
 * @method Championship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChampionshipRepository extends ServiceEntityRepository
{
    protected $security;
    public function __construct(ManagerRegistry $registry, TokenStorageInterface $security)
    {
        $this->security = $security;
        parent::__construct($registry, Championship::class);
    }

    /**
     * @return QueryBuilder
     */
    public function getCoachChampionships() {
        return $this->createQueryBuilder('champ')
            ->leftJoin('champ.managers', 'manager')
            ->where('manager = :coach')
            ->leftJoin('champ.teams', 'team')
            ->orWhere('team.coach = :coach')
            ->groupBy('champ.id')
            ->setParameter(':coach', $this->security->getToken()->getUser());
    }

    /**
     * @return Championship[]
     */
    public function findCoachChampionships() {
        $qb = $this->getCoachChampionships();
        return $qb->getQuery()->getResult();
    }

    /**
     * @return Championship[]
     */
    public function findCoachAllowedChampionships() {
        $qb = $this->getCoachChampionships();
        $qb
            ->leftJoin('champ.guests', 'guest')
            ->orWhere('guest = :coach')
            ->orWhere('champ.is_private = false');
        // TODO : add some other criteria
        return $qb->getQuery()->getResult();
    }
}
