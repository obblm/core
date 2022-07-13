<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Handler\League;

use Obblm\Core\Domain\Command\League\CreateLeagueCommand;
use Obblm\Core\Domain\Model\League;

interface CreateLeagueCommandHandlerInterface
{
    public function __invoke(CreateLeagueCommand $command): League;
}
