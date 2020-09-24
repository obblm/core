<?php

namespace Obblm\Core\Contracts;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface OptionableInterface
{
    public function __construct(array $options);
    public function resolveOptions($options);
    public function configureOptions(OptionsResolver $resolver);
}
