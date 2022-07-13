<?php

declare(strict_types=1);

namespace Obblm\Core\Tests\Fixtures\Command;

use Obblm\Core\Domain\Command\Coach\CreateCoachCommand;
use Obblm\Core\Tests\Fixtures\BuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CreateCoachCommandBuilder implements BuilderInterface
{
    public static function for(?ContainerInterface $container = null): self
    {
        return new CreateCoachCommandBuilder();
    }

    public function build()
    {
        return new CreateCoachCommand(
            random_bytes(8).'@'.random_bytes(8).'.com',
            random_bytes(8),
            random_bytes(8)
        );
    }
}
