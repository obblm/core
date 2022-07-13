<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Repository\Doctrine;

use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Model\League;
use Obblm\Core\Domain\Repository\LeagueRepositoryInterface;
use Obblm\Core\Infrastructure\Shared\Persistence\Doctrine\DoctrineRepository;

class LeagueRepository extends DoctrineRepository implements LeagueRepositoryInterface
{
    public function get($id): League
    {
        return $this->repository(League::class)->find($id);
    }

    public function findOneBy(array $criteria, array $orderBy = null) {
        return $this->repository(League::class)->findOneBy($criteria, $orderBy);
    }

    public function findAll() {
        return $this->repository(League::class)->findAll();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
        return $this->repository(League::class)->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findByAdmin(Coach $admin): array
    {
        return $this->repository(League::class)->findBy(['admin' => $admin]);
    }

    public function save(League $league): League
    {
        $this->persist($league);

        return $league;
    }

    public function delete(League $league): void
    {
        $this->remove($league);
    }
}
