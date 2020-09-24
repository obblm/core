<?php

namespace Obblm\Core\Helper\Rule\Inducement;

abstract class AbstractInducement
{
    /** @var string */
    protected $key;

    /** @var int */
    protected $value;

    /** @var int */
    protected $discount_value;

    /** @var string */
    protected $translation_key;

    /** @var string */
    protected $translation_domain;

    /** @var string */
    protected $translation_type;

    /** @var InducementType */
    protected $type;

    /** @var int */
    protected $max;

    /** @var array */
    protected $rosters = null;

    /**
     * Inducement constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->hydrateWithOptions($options);
    }

    protected function hydrateWithOptions($options)
    {
        $this->translation_type = $options['translation_type'] ?? false;
        $this->type = $options['type'] ?? null;
        $this->key = $options['key'] ?? false;
        $this->value = $options['value'] ?? false;
        $this->discount_value = $options['discount_value'] ?? $this->value;
        $this->translation_key = $options['translation_key'] ?? false;
        $this->translation_domain = $options['translation_domain'] ?? false;
        $this->max = $options['max'] ?? false;
        $this->rosters = $options['rosters'] ?? [];
    }

    /**
     * @return InducementType
     */
    public function getType(): ?InducementType
    {
        return $this->type;
    }

    public function isMultiple(): bool
    {
        return false;
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
    public function getTypeKey(): string
    {
        return $this->getType()->getKey();
    }

    /**
     * @return int
     */
    public function getDiscountValue(): int
    {
        return $this->discount_value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getTranslationType(): string
    {
        return $this->translation_type;
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

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * @return array
     */
    public function getRosters(): ?array
    {
        return $this->rosters;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @param int $value
     */
    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    /**
     * @param string $translation_key
     */
    public function setTranslationKey(string $translation_key): void
    {
        $this->translation_key = $translation_key;
    }

    /**
     * @param string $translation_domain
     */
    public function setTranslationDomain(string $translation_domain): void
    {
        $this->translation_domain = $translation_domain;
    }

    /**
     * @param string $translation_type
     */
    public function setTranslationType(string $translation_type): void
    {
        $this->translation_type = $translation_type;
    }

    /**
     * @param InducementType|array $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @param int $max
     */
    public function setMax(int $max): void
    {
        $this->max = $max;
    }

    /**
     * @param array $rosters
     */
    public function setRosters(array $rosters): void
    {
        $this->rosters = $rosters;
    }

    public function __toString(): string
    {
        return $this->translation_key;
    }
}
