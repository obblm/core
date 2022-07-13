<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Handler\League;

use Obblm\Core\Domain\Command\League\EditLeagueCommand;
use Obblm\Core\Domain\Model\League;

interface EditLeagueCommandHandlerInterface
{
    public function __invoke(EditLeagueCommand $command): League;
}
