<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ObblmCoreDomainExtension extends Extension
{
    public function getAlias()
    {
        return 'obblm';
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

        $container->setParameter('obblm.email_sender.email', $config['email_sender']['email']);
        $container->setParameter('obblm.email_sender.name', $config['email_sender']['name']);

        //$this->createAssetsDirectoriesConfiguration($container, $config);

        $locator = new FileLocator(dirname(__DIR__).'/Resources/config');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load('services.yaml');
    }

    private function createRulesPoolCacheDefinition(ContainerBuilder $container, array $config): string
    {
        $serviceId = 'obblm.cache.rules';
        $default = $container->getDefinition('obblm.cache');
        $default->addTag('cache.pool');
        $container->setDefinition('obblm.cache', $default);
        if ($config['caches']) {
            foreach ($config['caches'] as $part => $options) {
                if (isset($options['adapter'])) {
                }
            }
        }

        return $serviceId;
    }
}
