<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Service\Team;

use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Model\League;
use Obblm\Core\Domain\Repository\TeamRepositoryInterface;
use Obblm\Core\Domain\Service\MessageBusService;
use Symfony\Component\Messenger\MessageBusInterface;

class TeamService extends MessageBusService
{
    protected TeamRepositoryInterface $repository;

    public function __construct(MessageBusInterface $messageBus, TeamRepositoryInterface $repository)
    {
        parent::__construct($messageBus);
        $this->repository = $repository;
    }

    public function get(string $id): ?Team
    {
        return $this->repository->get($id);
    }

    /**
     * @return Team[]
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
