<?php

namespace Obblm\Core\Tests\Fixtures;

use Obblm\Core\Domain\Model\Rule;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RuleBuilder implements BuilderInterface
{
    public static function for(?ContainerInterface $container = null): RuleBuilder
    {
        return new RuleBuilder();
    }

    public function build()
    {
        return new Rule();
    }
}
