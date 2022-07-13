<?php

namespace Obblm\Core\Tests\Fixtures;

use Obblm\Core\Entity\Rule;

class RuleBuilder implements BuilderInterface
{
    public static function for(): RuleBuilder
    {
        return new RuleBuilder();
    }

    public function build()
    {
        return new Rule();
    }
}
