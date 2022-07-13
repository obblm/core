<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Handler\Doctrine\League;

use Obblm\Core\Domain\Command\League\CreateLeagueCommand;
use Obblm\Core\Domain\Handler\League\CreateLeagueCommandHandlerInterface;
use Obblm\Core\Domain\Model\League;
use Obblm\Core\Domain\Repository\LeagueRepositoryInterface;

class CreateLeagueCommandHandler implements CreateLeagueCommandHandlerInterface
{
    private LeagueRepositoryInterface $leagueRepository;

    public function __construct(LeagueRepositoryInterface $leagueRepository)
    {
        $this->leagueRepository = $leagueRepository;
    }

    public function __invoke(CreateLeagueCommand $command): League
    {
        $league = (new League())
            ->setName($command->getName())
            ->setAdmin($command->getAdmin())
        ;

        $this->leagueRepository->save($league);

        return $league;
    }
}
