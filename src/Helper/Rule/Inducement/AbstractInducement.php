<?php

namespace Obblm\Core\Helper\Rule\Inducement;

use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Helper\Optionable;
use Obblm\Core\Helper\Rule\Traits\TranslatableTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractInducement extends Optionable implements InducementInterface
{
    use TranslatableTrait;

    /** @var string */
    protected $key;
    /** @var int */
    protected $value;
    /** @var int */
    protected $discountValue;
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
        $this->key = $this->options['key'];
        $this->type = $this->options['type'];
        $this->name = $this->options['name'];
        $this->translationDomain = $this->options['translation_domain'];
        $this->value = $this->options['value'];
        $this->max = $this->options['max'];
        $this->typeName = $this->options['type_name'] = '';
        $this->discountValue = $this->options['discount_value'] ?? $this->value;
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
