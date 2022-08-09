<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\DependencyInjection;

use Obblm\Core\Domain\Helper\FileTeamUploader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\VarDumper\VarDumper;

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

        $locator = new FileLocator(dirname(__DIR__).'/Resources/config');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load('services.yaml');

        //$this->createRulesPoolCacheDefinition($container, $config);
        //$this->pullUploaders($container, $config);
    }

    private function pullUploaders(ContainerBuilder $container, array $config)
    {
        $teamUploaderDefinition = $container->getDefinition(FileTeamUploader::class);
        $teamUploaderDefinition->addMethodCall('setUploader', [new Reference('obblm.team.uploader')]);
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
