<?php

namespace Obblm\Core\Helper;

use Obblm\Core\Contracts\OptionableInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class Optionable implements OptionableInterface
{
    public function __construct(array $options = [])
    {
        $this->resolveOptions($options);
    }
    public function resolveOptions($options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }
}
