<?php

namespace Obblm\Core\Tests\Fixtures;

use Obblm\Core\Domain\Model\Team;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Uid\Uuid;

class TeamBuilder implements BuilderInterface
{
    public static function for(?ContainerInterface $container = null): TeamBuilder
    {
        return new TeamBuilder();
    }

    public function build(): Team
    {
        $rule = RuleBuilder::for()->build();

        return (new Team())
            ->setId(Uuid::v4())
            ->setName('Test Team')
            ->setRule($rule);
    }
}
