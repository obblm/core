<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Handler\Doctrine\Team;

use Obblm\Core\Domain\Command\Team\CreateTeamCommand;
use Obblm\Core\Domain\Handler\Team\CreateTeamCommandHandlerInterface;
use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Model\TeamVersion;
use Obblm\Core\Domain\Repository\TeamRepositoryInterface;

class CreateTeamCommandHandler implements CreateTeamCommandHandlerInterface
{
    private TeamRepositoryInterface $repository;

    public function __construct(TeamRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CreateTeamCommand $command): Team
    {
        $team = (new Team())
            ->setName($command->getName())
            ->setRoster($command->getRoster())
            ->setCoach($command->getCoach())
            ->setRule($command->getRule())
        ;

        $team->addVersion(new TeamVersion());

        $this->repository->save($team);

        return $team;
    }
}
