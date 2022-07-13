<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Repository;

use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Model\TeamVersion;

interface TeamVersionRepositoryInterface
{
    public function get($id): ?TeamVersion;

    /** @return TeamVersion[] */
    public function findByTeam(Team $team): array;

    public function save(TeamVersion $version): TeamVersion;

    public function delete(TeamVersion $version): void;
}
