<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Handler\Doctrine\League;

use Obblm\Core\Domain\Command\League\EditLeagueCommand;
use Obblm\Core\Domain\Handler\League\EditLeagueCommandHandlerInterface;
use Obblm\Core\Domain\Model\League;
use Obblm\Core\Domain\Repository\LeagueRepositoryInterface;

class EditLeagueCommandHandler implements EditLeagueCommandHandlerInterface
{
    private LeagueRepositoryInterface $leagueRepository;

    public function __construct(LeagueRepositoryInterface $leagueRepository)
    {
        $this->leagueRepository = $leagueRepository;
    }

    public function __invoke(EditLeagueCommand $command): League
    {
        $league = ($this->leagueRepository->get($command->getId()))
            ->setName($command->getName())
            ->setAdmin($command->getAdmin())
        ;

        $this->leagueRepository->save($league);

        return $league;
    }
}
