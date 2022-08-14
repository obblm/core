<?php

namespace Obblm\Core\Domain\Handler\Coach;

use Obblm\Core\Domain\Command\Coach\ActivateCoachCommand;
use Obblm\Core\Domain\Model\Coach;

interface ActivateCoachCommandInterface
{
    public function __invoke(ActivateCoachCommand $command): Coach;
}
