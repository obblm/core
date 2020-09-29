<?php

namespace Obblm\Core\Helper\Rule\Skill;

use Obblm\Core\Contracts\SkillInterface;
use Obblm\Core\Helper\Optionable;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractSkill extends Optionable implements SkillInterface
{
    /** @var string */
    private $key;
    /** @var string */
    private $name;
    /** @var string */
    private $translationDomain;
    /** @var string */
    private $type;
    /** @var string */
    private $typeName;
    /** @var string */
    private $description;

    protected function hydrateWithOptions()
    {
        $this->key = $this->options['key'];
        $this->name = $this->options['name'];
        $this->translationDomain = $this->options['translation_domain'];
        $this->type = $this->options['type'];
        $this->typeName = $this->options['type_name'];
        $this->description = $this->options['description'] ?? "";
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
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

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setTranslationDomain(string $translationDomain): self
    {
        $this->translationDomain = $translationDomain;
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

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'key'                => null,
            'type'               => null,
            'name'               => null,
            'translation_domain' => null,
            'type_name'          => null,
            'description'        => null,
        ])
            ->setRequired(['key', 'type', 'name', 'translation_domain', 'type_name'])
            ->setAllowedTypes('key', ['string'])
            ->setAllowedTypes('name', ['string'])
            ->setAllowedTypes('translation_domain', ['string'])
            ->setAllowedTypes('type', ['string'])
            ->setAllowedTypes('type_name', ['string'])
            ->setAllowedTypes('description', ['string', 'null'])
        ;
    }
}
