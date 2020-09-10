<?php

namespace Obblm\Core\DependencyInjection\CompilerPass;

use Obblm\Core\Service\RuleService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RulesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition(RuleService::class);

        foreach ($container->findTaggedServiceIds('obblm.rules') as $id => $tags) {
            $definition->addMethodCall('addRule', [new Reference($id)]);
        }
    }
}
