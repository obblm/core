<?php

namespace Obblm\Core\Helper\Rule\Inducement;

class InducementType
{
    /** @var string */
    public $key;

    /** @var string */
    private $translationKey;

    /** @var string */
    private $translationDomain;

    public function __construct(array $options = [])
    {
        $this->hydrateWithOptions($options);
    }

    private function hydrateWithOptions($options)
    {
        $this->key = $options['key'] ?? false;
        $this->translationKey = $options['translation_key'] ?? false;
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
     * @return string
     */
    public function getTranslationKey(): string
    {
        return $this->translationKey;
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
        return $this->translationKey;
    }
}
