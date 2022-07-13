<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Repository;

use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Model\Team;

interface TeamRepositoryInterface
{
    public function get($id): ?Team;

    /** @return Team[] */
    public function findByCoach(Coach $coach): array;

    public function findOneBy(array $criteria, array $orderBy = null);

    public function findAll();

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    public function save(Team $team): Team;

    public function delete(Team $team): void;
}
