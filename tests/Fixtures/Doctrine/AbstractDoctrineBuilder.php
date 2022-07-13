<?php

declare(strict_types=1);

namespace Obblm\Core\Tests\Fixtures\Doctrine;

use Obblm\Core\Tests\Fixtures\BuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractDoctrineBuilder implements BuilderInterface
{
    protected ContainerInterface $container;

    public static function for(?ContainerInterface $container): self
    {
        $class = get_called_class();

        return (new $class())->setContainer($container);
    }

    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;

        return $this;
    }

    abstract public function build();
}
