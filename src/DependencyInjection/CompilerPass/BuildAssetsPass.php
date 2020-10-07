<?php

namespace Obblm\Core\DependencyInjection\CompilerPass;

use Obblm\Core\Helper\AssetPackager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BuildAssetsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition(AssetPackager::class);

        foreach ($container->findTaggedServiceIds('obblm.asset_packager') as $id => $tags) {
            $definition->addMethodCall('addBuildAsset', [new Reference($id)]);
        }
    }
}
