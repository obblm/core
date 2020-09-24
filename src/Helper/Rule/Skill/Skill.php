<?php

namespace Obblm\Core\Helper\Rule\Skill;

use Obblm\Core\Helper\Optionable;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Skill extends Optionable
{
    /** @var string */
    private $key;

    /** @var string */
    private $type;

    /** @var string */
    private $translation_key;

    /** @var string */
    private $type_translation_key;

    /** @var string */
    private $translation_domain;

    /**
     * Inducement constructor.
     * @param array $options
     */

    private function hydrateWithOptions($options)
    {
        $this->key = $options['key'] ?? false;
        $this->type = $options['type'] ?? false;
        $this->translation_key = $options['translation_key'] ?? false;
        $this->type_translation_key = $options['type_translation_key'] ?? false;
        $this->translation_domain = $options['translation_domain'] ?? false;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTranslationKey(): string
    {
        return $this->translation_key;
    }

    /**
     * @param string $translation_key
     */
    public function setTranslationKey(string $translation_key): void
    {
        $this->translation_key = $translation_key;
    }

    /**
     * @return string
     */
    public function getTypeTranslationKey(): string
    {
        return $this->type_translation_key;
    }

    /**
     * @param string $type_translation_key
     */
    public function setTypeTranslationKey(string $type_translation_key): void
    {
        $this->type_translation_key = $type_translation_key;
    }

    /**
     * @return string
     */
    public function getTranslationDomain(): string
    {
        return $this->translation_domain;
    }

    /**
     * @param string $translation_domain
     */
    public function setTranslationDomain(string $translation_domain): void
    {
        $this->translation_domain = $translation_domain;
    }

    public function __toString(): string
    {
        return $this->translation_key;
    }

    public function resolveOptions($options)
    {
        parent::resolveOptions($options);
        $this->hydrateWithOptions($options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'key'                  => null,
            'type'                 => null,
            'translation_key'      => null,
            'translation_domain'   => null,
            'type_translation_key' => null,
        ])
            ->setRequired('key')
            ->setRequired('translation_key')
            ->setRequired('translation_domain')
            ->setRequired('type')
            ->setRequired('type_translation_key')
            ->setAllowedTypes('key', ['string'])
            ->setAllowedTypes('translation_key', ['string'])
            ->setAllowedTypes('translation_domain', ['string'])
            ->setAllowedTypes('type', ['string'])
            ->setAllowedTypes('type_translation_key', ['string'])
        ;
    }
}
