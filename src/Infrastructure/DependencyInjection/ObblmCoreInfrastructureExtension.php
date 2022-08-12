<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class ObblmCoreInfrastructureExtension extends Extension
{
    public function getAlias(): string
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

        $this->bindUploaders($container, $config);

        $loader->load('services.yaml');
    }

    private function bindUploaders(ContainerBuilder $container, array $config)
    {
        if (isset($config['uploads']['team'])) {
            $this->bindTeamUploader($container, $config['uploads']['team']);
        }
    }

    private function bindTeamUploader(ContainerBuilder $container, array $config)
    {
        $teamUploaderDefinition = $this->getFileUploader($container, $config);
        $container->setDefinition('obblm.team.uploader', $teamUploaderDefinition);
    }

    private function getFileUploader(ContainerBuilder $container, array $config): Definition
    {
        switch ($config['class']) {
            default:
                $slugger = new Reference('Symfony\\Component\\String\\Slugger\\SluggerInterface');

                return (new Definition($config['class'], [
                        '$targetDirectory' => $config['path'],
                        '$slugger' => $slugger,
                    ]))->setPublic(true);
        }
    }
}
