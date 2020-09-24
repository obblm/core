<?php

namespace Obblm\Core\Helper\Rule\Inducement;

class InducementType
{
    /** @var string */
    public $key;

    /** @var string */
    private $translation_key;

    /** @var string */
    private $translation_domain;

    public function __construct(array $options = [])
    {
        $this->hydrateWithOptions($options);
    }

    private function hydrateWithOptions($options)
    {
        $this->key = $options['key'] ?? false;
        $this->translation_key = $options['translation_key'] ?? false;
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
     * @return string
     */
    public function getTranslationKey(): string
    {
        return $this->translation_key;
    }

    /**
     * @return string
     */
    public function getTranslationDomain(): string
    {
        return $this->translation_domain;
    }

    public function __toString(): string
    {
        return $this->translation_key;
    }
}
