<?php

namespace Obblm\Core\Tests\Fixtures;

use Symfony\Component\DependencyInjection\ContainerInterface;

interface BuilderInterface
{
    public static function for(?ContainerInterface $container): self;

    public function build();
}
