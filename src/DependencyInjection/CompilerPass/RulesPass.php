<?php

namespace Obblm\Core\DependencyInjection\CompilerPass;

use Obblm\Core\Helper\RuleHelper;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RulesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition(RuleHelper::class);

        foreach ($container->findTaggedServiceIds('obblm.rule_helpers') as $id => $tags) {
            $definition->addMethodCall('addHelper', [new Reference($id)]);
        }
    }
}
