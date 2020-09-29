<?php

namespace Obblm\Core\Helper\Rule\Inducement;

use Obblm\Core\Helper\Optionable;
use Obblm\Core\Helper\Rule\Translatable;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InducementType extends Optionable implements Translatable
{
    /** @var string */
    public $key;
    /** @var string */
    private $name;
    /** @var string */
    private $translationDomain;

    protected function hydrateWithOptions()
    {
        $this->key = $this->options['key'];
        $this->name = $this->options['name'];
        $this->translationDomain = $this->options['translation_domain'];
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'key'                => null,
            'name'               => null,
            'translation_domain' => null,
        ])
            ->setRequired(['key', 'name', 'translation_domain'])
            ->setAllowedTypes('key', ['string'])
            ->setAllowedTypes('name', ['string'])
            ->setAllowedTypes('translation_domain', ['string'])
        ;
    }
}
