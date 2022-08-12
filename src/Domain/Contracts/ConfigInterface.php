<?php

namespace Obblm\Core\Domain\Contracts;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface ConfigInterface
{
    public static function getChildren(): array;

    public function configureOptions(OptionsResolver $resolver);
}
