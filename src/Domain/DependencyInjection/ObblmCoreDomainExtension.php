<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\DependencyInjection;

use Obblm\Core\Domain\Contracts\DefaultSenderInterface;
use Obblm\Core\Domain\Contracts\RuleHelperInterface;
use Obblm\Core\Domain\DependencyInjection\CompilerPass\DefaultMailSenderPass;
use Obblm\Core\Domain\DependencyInjection\CompilerPass\RulesPass;
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

        $container->setParameter(DefaultMailSenderPass::SENDER_ADDRESS, $config['email_sender']['email']);
        $container->setParameter(DefaultMailSenderPass::SENDER_NAME, $config['email_sender']['name']);

        $locator = new FileLocator(dirname(__DIR__).'/Resources/config');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(RuleHelperInterface::class)
            ->addTag(RulesPass::SERVICE_TAG)
        ;

        $container->registerForAutoconfiguration(DefaultSenderInterface::class)
            ->addTag(DefaultMailSenderPass::SERVICE_TAG)
        ;
    }
}
