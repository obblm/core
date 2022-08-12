<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Repository\Doctrine;

use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Repository\TeamRepositoryInterface;
use Obblm\Core\Infrastructure\Shared\Persistence\Doctrine\DoctrineRepository;

class TeamRepository extends DoctrineRepository implements TeamRepositoryInterface
{
    public function get($id): Team
    {
        return $this->repository(Team::class)->find($id);
    }

    public function findByCoach(Coach $coach): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('t')
            ->from(Team::class, 't')
            ->where('t.coach=:coach')
            ->setParameter(':coach', $coach)
            ->orderBy('t.updatedAt', 'DESC')
            ->orderBy('t.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->repository(Team::class)->findOneBy($criteria, $orderBy);
    }

    public function findAll()
    {
        return $this->repository(Team::class)->findAll();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository(Team::class)->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function save(Team $team): Team
    {
        $this->persist($team);

        return $team;
    }

    public function delete(Team $team): void
    {
        $this->remove($team);
    }
}
