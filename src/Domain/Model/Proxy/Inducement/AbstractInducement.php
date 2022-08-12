<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model\Proxy\Inducement;

use Obblm\Core\Domain\Contracts\OptionableInterface;
use Obblm\Core\Domain\Contracts\Rule\InducementInterface;
use Obblm\Core\Domain\Model\Proxy\Traits\OptionableTrait;
use Obblm\Core\Domain\Model\Proxy\Traits\TranslatableTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractInducement implements InducementInterface, OptionableInterface
{
    use TranslatableTrait;
    use OptionableTrait;

    protected string $key;
    protected int $value;
    protected int $discountValue;
    protected string $typeName;
    protected InducementType $type;
    protected int $max;
    protected ?string $partOf = null;
    protected ?array $rosters = null;

    protected function hydrateWithOptions()
    {
        $this->key = $this->options['key'];
        $this->type = $this->options['type'];
        $this->name = $this->options['name'];
        $this->translationDomain = $this->options['translation_domain'];
        $this->value = $this->options['value'];
        $this->max = $this->options['max'];
        $this->partOf = $this->options['part_of'];
        $this->typeName = $this->options['type_name'] = '';
        $this->discountValue = $this->options['discount_value'] ?? $this->value;
        $this->rosters = $this->options['rosters'] ?? [];
    }

    public function getType(): ?InducementType
    {
        return $this->type;
    }

    public function isMultiple(): bool
    {
        return false;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getTypeKey(): string
    {
        return $this->getType()->getKey();
    }

    public function getDiscountValue(): int
    {
        return $this->discountValue;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getTypeName(): string
    {
        return $this->getType()->getName();
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function getPartOf(): ?string
    {
        return $this->partOf;
    }

    public function getRosters(): ?array
    {
        return $this->rosters;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function setType(InducementType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setMax(int $max): self
    {
        $this->max = $max;

        return $this;
    }

    public function setPartOf(?string $partOf): self
    {
        $this->partOf = $partOf;

        return $this;
    }

    public function setRosters(array $rosters): self
    {
        $this->rosters = $rosters;

        return $this;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'key' => null,
            'type' => null,
            'name' => null,
            'translation_domain' => null,
            'value' => null,
            'discount_value' => null,
            'max' => null,
            'part_of' => null,
            'rosters' => null,
        ])
            ->setRequired(['key', 'type', 'name', 'translation_domain', 'value', 'max'])
            ->setAllowedTypes('key', ['string'])
            ->setAllowedTypes('type', [InducementType::class])
            ->setAllowedTypes('name', ['string'])
            ->setAllowedTypes('translation_domain', ['string'])
            ->setAllowedTypes('value', ['int'])
            ->setAllowedTypes('discount_value', ['int', 'null'])
            ->setAllowedTypes('part_of', ['string', 'null'])
            ->setAllowedTypes('max', ['int'])
            ->setAllowedTypes('rosters', ['array', 'null'])
        ;
    }
}
