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
    private $domain;
    /** @var string */
    private $type;
    /** @var string */
    private $typeName;
    /** @var string */
    private $description;

    protected function hydrateWithOptions()
    {
        $this->key = $this->options['key'] ?? false;
        $this->name = $this->options['name'] ?? false;
        $this->domain = $this->options['domain'] ?? false;
        $this->type = $this->options['type'] ?? false;
        $this->typeName = $this->options['type_name'] ?? false;
        $this->description = $this->options['description'] ?? false;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDomain(): string
    {
        return $this->domain;
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

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;
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

    public function getTranslationDomain(): string
    {
        return $this->domain;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'key'       => null,
            'type'      => null,
            'name'      => null,
            'domain'    => null,
            'type_name' => null,
            'description' => null,
        ])
            ->setRequired(['key', 'type', 'name', 'domain', 'type_name'])
            ->setAllowedTypes('key', ['string'])
            ->setAllowedTypes('name', ['string'])
            ->setAllowedTypes('domain', ['string'])
            ->setAllowedTypes('type', ['string'])
            ->setAllowedTypes('type_name', ['string'])
            ->setAllowedTypes('description', ['string', 'null'])
        ;
    }
}
