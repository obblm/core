<?php

declare(strict_types=1);

namespace Obblm\Core\Application\DependencyInjection\CompilerPass;

use Obblm\Core\Application\Service\AssetPackager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BuildAssetsPass implements CompilerPassInterface
{
    const PASS_TAG_ID = 'obblm.asset_packager';

    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition(AssetPackager::class);

        foreach ($container->findTaggedServiceIds(self::PASS_TAG_ID) as $id => $tags) {
            $definition->addMethodCall('addBuildAsset', [new Reference($id)]);
        }
    }
}
