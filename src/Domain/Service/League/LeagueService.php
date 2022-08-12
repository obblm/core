<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Service\League;

use Obblm\Core\Domain\Contracts\ObblmBusInterface;
use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Model\League;
use Obblm\Core\Domain\Repository\LeagueRepositoryInterface;
use Obblm\Core\Domain\Service\MessageBusService;

class LeagueService extends MessageBusService
{
    protected LeagueRepositoryInterface $repository;

    public function __construct(ObblmBusInterface $messageBus, LeagueRepositoryInterface $repository)
    {
        parent::__construct($messageBus);
        $this->repository = $repository;
    }

    public function get(string $id): ?League
    {
        return $this->repository->get($id);
    }

    /**
     * @return League[]
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function supportsClass(string $class): bool
    {
        return Coach::class === $class;
    }
}
