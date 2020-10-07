<?php

namespace Obblm\Core;

use Obblm\Core\DependencyInjection\CompilerPass\BuildAssetsPass;
use Obblm\Core\DependencyInjection\CompilerPass\RoutesPass;
use Obblm\Core\DependencyInjection\CompilerPass\RulesPass;
use Obblm\Core\DependencyInjection\ObblmCoreExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ObblmCoreBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new ObblmCoreExtension();
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new RulesPass());
        $container->addCompilerPass(new RoutesPass());
        $container->addCompilerPass(new BuildAssetsPass());
    }
}
