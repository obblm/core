<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Handler\Team;

use Obblm\Core\Domain\Command\Team\CreateTeamCommand;
use Obblm\Core\Domain\Model\Team;

interface CreateTeamCommandHandlerInterface
{
    public function __invoke(CreateTeamCommand $command): Team;
}
