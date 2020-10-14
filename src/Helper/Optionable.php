<?php

namespace Obblm\Core\Helper;

use Obblm\Core\Contracts\OptionableInterface;
use Obblm\Core\Exception\NotFoundKeyException;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class Optionable implements OptionableInterface
{
    /** @var array */
    protected $options;

    public function __construct(array $options = [])
    {
        $this->resolveOptions($options);
        $this->hydrateWithOptions();
    }

    abstract protected function hydrateWithOptions();

    public function resolveOptions($options):void
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    public function getOption(string $key)
    {
        if (!isset($this->options[$key])) {
            throw new NotFoundKeyException($key, 'options', self::class);
        }
        return $this->options[$key];
    }
}
