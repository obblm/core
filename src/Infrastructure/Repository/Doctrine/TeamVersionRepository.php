<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Repository\Doctrine;

use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Model\TeamVersion;
use Obblm\Core\Domain\Repository\TeamVersionRepositoryInterface;
use Obblm\Core\Infrastructure\Shared\Persistence\Doctrine\DoctrineRepository;

/**
 * @method TeamVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeamVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeamVersion[]    findAll()
 * @method TeamVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamVersionRepository extends DoctrineRepository implements TeamVersionRepositoryInterface
{
    public function get($id): TeamVersion
    {
        return $this->repository(TeamVersion::class)->find($id);
    }

    public function findByTeam(Team $team): array
    {
        return $this->repository(TeamVersion::class)->findBy(['team' => $team]);
    }

    public function save(TeamVersion $version): TeamVersion
    {
        $this->persist($version);

        return $version;
    }

    public function delete(TeamVersion $version): void
    {
        $this->remove($version);
    }
}
