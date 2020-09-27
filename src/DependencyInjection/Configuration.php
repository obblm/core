<?php

namespace Obblm\Core\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('obblm');
        $rootNode = $treeBuilder->getRootNode();
        $treeBuilder->getRootNode()
                ->children()
                    ->scalarNode('upload_directory')
                    ->end()
                ->end()
            ->end()
        ;
        $this->getCacheTree($rootNode);

        return $treeBuilder;
    }
    private function getCacheTree(ArrayNodeDefinition $rootNode)
    {
        return $rootNode
            ->fixXmlConfig('cache')
            ->children()
                ->arrayNode('caches')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('rules')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('adapter')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->defaultValue('app.cache')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
