<?php

namespace Obblm\Core\DependencyInjection;

use Obblm\Core\Service\Rule\RuleInterface;
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

        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(RuleInterface::class)
            ->addTag('obblm.rules')
        ;
    }
}