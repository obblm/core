<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Command\Coach;

use Obblm\Core\Domain\Command\CommandInterface;

class ActivateCoachCommand implements CommandInterface
{
    private string $hash;

    public function __construct(string $hash)
    {
        $this->hash = $hash;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
