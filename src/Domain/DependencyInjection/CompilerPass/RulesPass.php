<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\DependencyInjection\CompilerPass;

use Obblm\Core\Domain\Service\Rule\RuleService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RulesPass implements CompilerPassInterface
{
    const SERVICE_TAG = 'obblm.rule_helpers';

    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition(RuleService::class);

        foreach ($container->findTaggedServiceIds(self::SERVICE_TAG) as $id => $tags) {
            $definition->addMethodCall('addHelper', [new Reference($id)]);
        }
    }
}
