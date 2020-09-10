<?php

namespace Obblm\Core;

use Obblm\Core\DependencyInjection\CompilerPass\RulesPass;
use Obblm\Core\DependencyInjection\ObblmCoreExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use function dirname;
use const PHP_VERSION_ID;

class ObblmCoreBundle extends Bundle
{
    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getContainerExtension()
    {
        return new ObblmCoreExtension();
    }

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RulesPass());
    }
}
