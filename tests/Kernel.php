<?php

declare(strict_types=1);

namespace Obblm\Core\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Obblm\Core\ObblmCoreBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Twig\Extra\TwigExtraBundle\TwigExtraBundle;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('config/{packages}/*.yaml');
        $container->import('config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file(\dirname(__DIR__).'/config/services.yaml')) {
            $container->import('config/services.yaml');
            $container->import('config/{services}_'.$this->environment.'.yaml');
        } elseif (is_file($path = \dirname(__FILE__).'/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import('config/{routes}/*.yaml');

        if (is_file(\dirname(__FILE__).'/config/routes.yaml')) {
            $routes->import('config/routes.yaml');
        } elseif (is_file($path = \dirname(__FILE__).'/config/routes.php')) {
            (require $path)($routes->withPath($path), $this);
        }
    }

    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new TwigExtraBundle(),
            new DoctrineBundle(),
            new DoctrineMigrationsBundle(),
            new SecurityBundle(),
            new ObblmCoreBundle(),
        ];
    }
}
