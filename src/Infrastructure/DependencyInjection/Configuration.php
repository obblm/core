<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\DependencyInjection;

use Obblm\Core\Infrastructure\Uploader\LocalFileUploader;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('obblm_infrastructure');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->arrayNode('uploads')->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('team')->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue(LocalFileUploader::class)->isRequired()->end()
                            ->scalarNode('path')->defaultValue('%kernel.project_dir%/var/uploads/team')->end()
                        ->end()
                    ->end()
                    ->arrayNode('league')->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->defaultValue(LocalFileUploader::class)->isRequired()->end()
                            ->scalarNode('path')->defaultValue('%kernel.project_dir%/var/uploads/league')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
