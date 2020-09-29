<?php

namespace Obblm\Core\Helper\Rule\Inducement;

use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Helper\Optionable;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractInducement extends Optionable implements InducementInterface
{
    /** @var string */
    protected $key;
    /** @var int */
    protected $value;
    /** @var int */
    protected $discountValue;
    /** @var string */
    protected $name;
    /** @var string */
    protected $translationDomain;
    /** @var string */
    protected $typeName;
    /** @var InducementType */
    protected $type;
    /** @var int */
    protected $max;
    /** @var array */
    protected $rosters = null;

    protected function hydrateWithOptions()
    {
        $this->key = $this->options['key'] ?? false;
        $this->type = $this->options['type'] ?? null;
        $this->name = $this->options['name'] ?? false;
        $this->translationDomain = $this->options['translation_domain'] ?? false;
        $this->typeName = $this->options['type_name'] ?? false;
        $this->value = $this->options['value'] ?? false;
        $this->discountValue = $this->options['discount_value'] ?? $this->value;
        $this->max = $this->options['max'] ?? false;
        $this->rosters = $this->options['rosters'] ?? [];
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
    public function getTypeName(): string
    {
        return $this->getType()->getName();
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
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string $translationDomain
     */
    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
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
        return $this->name;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'key'                => null,
            'type'               => null,
            'name'               => null,
            'translation_domain' => null,
            'value'              => null,
            'discount_value'     => null,
            'max'                => null,
            'rosters'            => null,
        ])
            ->setRequired(['key', 'type', 'name', 'translation_domain', 'value', 'max'])
            ->setAllowedTypes('key', ['string'])
            ->setAllowedTypes('type', [InducementType::class])
            ->setAllowedTypes('name', ['string'])
            ->setAllowedTypes('translation_domain', ['string'])
            ->setAllowedTypes('value', ['int'])
            ->setAllowedTypes('discount_value', ['int', 'null'])
            ->setAllowedTypes('max', ['int'])
            ->setAllowedTypes('rosters', ['array', 'null'])
        ;
    }
}
