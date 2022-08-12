<?php

namespace Obblm\Core\Domain\Model\Proxy\Skill;

use Obblm\Core\Domain\Contracts\Rule\SkillInterface;
use Obblm\Core\Domain\Model\Proxy\Traits\OptionableTrait;
use Obblm\Core\Domain\Model\Proxy\Traits\TranslatableTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractSkill implements SkillInterface
{
    use TranslatableTrait;
    use OptionableTrait;

    /** @var string */
    private $key;
    /** @var string */
    private $type;
    /** @var string */
    private $typeName;
    /** @var string */
    private $description;

    protected function hydrateWithOptions()
    {
        $this
            ->setKey($this->options['key'])
            ->setName($this->options['name'])
            ->setNameWithVars($this->options['name_with_vars'])
            ->setTranslationDomain($this->options['translation_domain'])
            ->setTranslationVars($this->options['translation_vars'])
            ->setType($this->options['type'])
            ->setTypeName($this->options['type_name'])
            ->setDescription($this->options['description'] ?? '');
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTypeName(): string
    {
        return $this->typeName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function setTypeName(string $typeName): self
    {
        $this->typeName = $typeName;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'key' => null,
            'type' => null,
            'name' => null,
            'name_with_vars' => null,
            'translation_domain' => null,
            'translation_vars' => [],
            'type_name' => null,
            'description' => null,
        ])
            ->setRequired(['key', 'type', 'name', 'translation_domain', 'type_name'])
            ->setAllowedTypes('key', ['string'])
            ->setAllowedTypes('name', ['string'])
            ->setAllowedTypes('name_with_vars', ['string', 'null'])
            ->setAllowedTypes('translation_domain', ['string'])
            ->setAllowedTypes('translation_vars', ['array'])
            ->setAllowedTypes('type', ['string'])
            ->setAllowedTypes('type_name', ['string'])
            ->setAllowedTypes('description', ['string', 'null'])
        ;
    }
}
