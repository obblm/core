<?php

namespace Obblm\Core\DependencyInjection;

use Obblm\Core\Routing\AutoloadedRouteInterface;
use Obblm\Core\Helper\Rule\RuleHelperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ObblmCoreExtension extends Extension {

    public function load(array $configs, ContainerBuilder $container)
    {
        $config = [];
        // let resources override the previous set value
        foreach ($configs as $subConfig) {
            $config = array_merge($config, $subConfig);
        }
        $locator = new FileLocator(dirname(__DIR__).'/Resources/config');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(RuleHelperInterface::class)
            ->addTag('obblm.rule_helpers')
        ;
        $container->registerForAutoconfiguration(AutoloadedRouteInterface::class)
            ->addTag('obblm.routes')
        ;
    }
}