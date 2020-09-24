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
    private $translationKey;

    /** @var string */
    private $typeTranslationKey;

    /** @var string */
    private $translationDomain;

    /**
     * Inducement constructor.
     * @param array $options
     */

    private function hydrateWithOptions($options)
    {
        $this->key = $options['key'] ?? false;
        $this->type = $options['type'] ?? false;
        $this->translationKey = $options['translation_key'] ?? false;
        $this->typeTranslationKey = $options['type_translation_key'] ?? false;
        $this->translationDomain = $options['translation_domain'] ?? false;
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
        return $this->translationKey;
    }

    /**
     * @param string $translationKey
     */
    public function setTranslationKey(string $translationKey): void
    {
        $this->translationKey = $translationKey;
    }

    /**
     * @return string
     */
    public function getTypeTranslationKey(): string
    {
        return $this->typeTranslationKey;
    }

    /**
     * @param string $typeTranslationKey
     */
    public function setTypeTranslationKey(string $typeTranslationKey): void
    {
        $this->typeTranslationKey = $typeTranslationKey;
    }

    /**
     * @return string
     */
    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    /**
     * @param string $translationDomain
     */
    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    public function __toString(): string
    {
        return $this->translationKey;
    }

    public function resolveOptions($options):void
    {
        parent::resolveOptions($options);
        $this->hydrateWithOptions($options);
    }

    public function configureOptions(OptionsResolver $resolver):void
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
