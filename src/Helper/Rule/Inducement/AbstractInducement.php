<?php

namespace Obblm\Core\Helper\Rule\Inducement;

abstract class AbstractInducement
{
    /** @var string */
    protected $key;

    /** @var int */
    protected $value;

    /** @var int */
    protected $discountValue;

    /** @var string */
    protected $translationKey;

    /** @var string */
    protected $translationDomain;

    /** @var string */
    protected $translationType;

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
        $this->translationType = $options['translation_type'] ?? false;
        $this->type = $options['type'] ?? null;
        $this->key = $options['key'] ?? false;
        $this->value = $options['value'] ?? false;
        $this->discountValue = $options['discount_value'] ?? $this->value;
        $this->translationKey = $options['translation_key'] ?? false;
        $this->translationDomain = $options['translation_domain'] ?? false;
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
        return $this->discountValue;
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
        return $this->translationType;
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
     * @param string $translationKey
     */
    public function setTranslationKey(string $translationKey): void
    {
        $this->translationKey = $translationKey;
    }

    /**
     * @param string $translationDomain
     */
    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    /**
     * @param string $translationType
     */
    public function setTranslationType(string $translationType): void
    {
        $this->translationType = $translationType;
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
        return $this->translationKey;
    }
}
