<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\DependencyInjection;

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

        return $treeBuilder;
    }
}
