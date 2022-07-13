<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model\Proxy\Traits;

use Obblm\Core\Domain\Exception\NotFoundKeyException;
use Symfony\Component\OptionsResolver\OptionsResolver;

trait OptionableTrait
{
    protected array$options;

    public function setOptions(array $options = []): void
    {
        $this->resolveOptions($options);
        $this->hydrateWithOptions();
    }

    abstract protected function configureOptions(OptionsResolver $resolver);

    abstract protected function hydrateWithOptions();

    public function resolveOptions($options): void
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * @return mixed
     * @throw NotFoundKeyException
     */
    public function getOption(string $key)
    {
        if (!isset($this->options[$key])) {
            throw new NotFoundKeyException($key, 'options', self::class);
        }

        return $this->options[$key];
    }
}
