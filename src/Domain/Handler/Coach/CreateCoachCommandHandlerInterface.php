<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Handler\Coach;

use Obblm\Core\Domain\Command\Coach\CreateCoachCommand;
use Obblm\Core\Domain\Model\Coach;

interface CreateCoachCommandHandlerInterface
{
    public function __invoke(CreateCoachCommand $command): Coach;
}
