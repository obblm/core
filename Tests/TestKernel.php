<?php

namespace Obblm\Core\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Obblm\Core\ObblmCoreBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class TestKernel extends BaseKernel
{
    private $configurationFilename;

    /**
     * Defines the configuration filename.
     *
     * @param string $filename
     */
    public function setConfigurationFilename($filename)
    {
        $this->configurationFilename = $filename;
    }

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new SecurityBundle(),
            new ObblmCoreBundle(),
        ];
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        echo dirname(__DIR__ . PHP_EOL);
        $container->import(dirname(__DIR__) . '/config/{packages}/*.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(dirname(__DIR__) . '/config/{routes}/*.yaml');
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->configurationFilename);
    }
}
