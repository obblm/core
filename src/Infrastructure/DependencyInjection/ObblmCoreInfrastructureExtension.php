<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ObblmCoreInfrastructureExtension extends Extension
{
    public function getAlias()
    {
        return 'obblm_infrastructure';
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($configs as $subConfig) {
            if ($subConfig) {
                $config = array_merge($config, $subConfig);
            }
        }

        $locator = new FileLocator(dirname(__DIR__).'/Resources/config');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load('services.yaml');
    }
}
