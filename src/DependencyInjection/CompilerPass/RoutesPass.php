<?php

namespace Obblm\Core\DependencyInjection\CompilerPass;

use Obblm\Core\Routing\RouteAutoloader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RoutesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition(RouteAutoloader::class);

        foreach ($container->findTaggedServiceIds('obblm.routes') as $id => $tags) {
            $definition->addMethodCall('addObblmRoute', [new Reference($id)]);
        }
    }
}
