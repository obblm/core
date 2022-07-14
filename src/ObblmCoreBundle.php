<?php

declare(strict_types=1);

namespace Obblm\Core;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Obblm\Core\Application\DependencyInjection\CompilerPass\BuildAssetsPass;
use Obblm\Core\Application\DependencyInjection\ObblmCoreApplicationExtension;
use Obblm\Core\Domain\DependencyInjection\ObblmCoreDomainExtension;
use Obblm\Core\Infrastructure\DependencyInjection\ObblmCoreInfrastructureExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ObblmCoreBundle extends Bundle
{
    protected $path = __DIR__ . DIRECTORY_SEPARATOR . 'Application';

    public function getContainerExtension()
    {
        return new ObblmCoreDomainExtension();
    }

    public function build(ContainerBuilder $container): void
    {
        $container->registerExtension(new ObblmCoreInfrastructureExtension());
        $container->registerExtension(new ObblmCoreApplicationExtension());

        parent::build($container);
        //$container->addCompilerPass(new RulesPass());
        //$container->addCompilerPass(new RoutesPass());
        $container->addCompilerPass(new BuildAssetsPass());

        $this->buildMappingCompilerPass($container);
    }

    private function buildMappingCompilerPass($container)
    {
        $productMappings = [
            realpath(__DIR__.'/Infrastructure/Resources/doctrine') => 'Obblm\Core\Domain\Model',
        ];

        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createXmlMappingDriver(
                $productMappings,
                ['doctrine.orm.entity_manager'],
                false
            )
        );
    }
}
