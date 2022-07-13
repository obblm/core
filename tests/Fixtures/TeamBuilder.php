<?php

namespace Obblm\Core\Tests\Fixtures;

use Obblm\Core\Entity\Team;

class TeamBuilder implements BuilderInterface
{
    public static function for(): TeamBuilder
    {
        return new TeamBuilder();
    }

    public function build(): Team
    {
        $rule = RuleBuilder::for()->build();

        return (new Team())
            ->setName('Test Team')
            ->setRule($rule);
    }
}
