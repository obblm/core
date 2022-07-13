<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Repository\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Obblm\Core\Infrastructure\Model\Doctrine\Player;
use Obblm\Core\Infrastructure\Model\Doctrine\PlayerVersion;
use Obblm\Core\Infrastructure\Model\Doctrine\TeamVersion;

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

    public function findNotDeadPlayerVersionsByTeamVersion(TeamVersion $version)
    {
        $qb = $this->createQueryBuilder('pv')
            ->where('pv.teamVersion = :version')
            ->andWhere('pv.dead = :dead')
            ->leftJoin(Player::class, 'p', Join::WITH, 'p = pv.player')
            ->orderBy('p.number', 'ASC')
            ->setParameter('version', $version)
            ->setParameter('dead', false)
        ;

        return $qb->getQuery()->getResult();
    }
}
