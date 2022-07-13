<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model\Proxy\Inducement;

use Obblm\Core\Domain\Contracts\OptionableInterface;
use Obblm\Core\Domain\Model\Proxy\Traits\OptionableTrait;
use Obblm\Core\Domain\Model\Proxy\Traits\TranslatableTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InducementType implements OptionableInterface
{
    use TranslatableTrait;
    use OptionableTrait;

    public string $key;

    protected function hydrateWithOptions()
    {
        $this->key = $this->options['key'];
        $this->name = $this->options['name'];
        $this->translationDomain = $this->options['translation_domain'];
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'key' => null,
            'name' => null,
            'translation_domain' => null,
        ])
            ->setRequired(['key', 'name', 'translation_domain'])
            ->setAllowedTypes('key', ['string'])
            ->setAllowedTypes('name', ['string'])
            ->setAllowedTypes('translation_domain', ['string'])
        ;
    }
}
