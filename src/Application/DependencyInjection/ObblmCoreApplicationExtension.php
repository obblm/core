<?php

declare(strict_types=1);

namespace Obblm\Core\Application\DependencyInjection;

use Obblm\Core\Application\DependencyInjection\CompilerPass\BuildAssetsPass;
use Obblm\Core\Domain\Contracts\BuildAssetsInterface;
use Obblm\Core\Infrastructure\DependencyInjection\Configuration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ObblmCoreApplicationExtension extends Extension implements PrependExtensionInterface
{
    public function getAlias()
    {
        return 'obblm_application';
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('twig', [
            'paths' => [
                dirname(__DIR__).'/Resources/views' => 'ObblmCoreApplication',
                dirname(__DIR__).'/Resources/public' => 'ObblmCoreAssets',
            ],
        ]);

        $container->prependExtensionConfig('framework', [
            'translator' => [
                'paths' => [
                    dirname(__DIR__).'/Resources/translations',
                ],
            ],
        ]);
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

        $container->registerForAutoconfiguration(BuildAssetsInterface::class)
            ->addTag(BuildAssetsPass::PASS_TAG_ID)
        ;
    }

    private function createAssetsDirectoriesConfiguration(ContainerBuilder $container, $config)
    {
        // Upload
        $uploadDirectory = (!isset($config['obblm.upload_directory'])) ?
            $container->getParameter('kernel.project_dir').'/Resources/public/obblm/uploads' :
            $config['obblm.upload_directory'];
        $container->setParameter('obblm.config.directory.upload', $uploadDirectory);

        // Image resize cache
        $uploadDirectory = (!isset($config['obblm.public_cache_directory'])) ?
            $container->getParameter('kernel.project_dir').'/Resources/public/obblm/cache' :
            $config['obblm.public_cache_directory'];
        $container->setParameter('obblm.config.directory.public.cache', $uploadDirectory);
    }
}
