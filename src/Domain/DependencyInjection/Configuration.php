<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('obblm');
        $rootNode = $treeBuilder->getRootNode();
        $treeBuilder->getRootNode()
                ->children()
                    ->arrayNode('email_sender')
                    ->isRequired()
                    ->children()
                        ->scalarNode('email')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end()
        ;
        $this->getCacheTree($rootNode);

        return $treeBuilder;
    }

    private function getCacheTree(ArrayNodeDefinition $rootNode): ArrayNodeDefinition
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
            ->end();
    }
}
