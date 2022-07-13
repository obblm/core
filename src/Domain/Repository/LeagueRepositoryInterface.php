<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Repository;

use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Model\League;

interface LeagueRepositoryInterface
{
    public function get($id): ?League;

    /** @return League[] */
    public function findByAdmin(Coach $admin): array;

    public function findOneBy(array $criteria, array $orderBy = null);

    public function findAll();

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    public function save(League $league): League;

    public function delete(League $league): void;
}
