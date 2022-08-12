<?php

namespace Obblm\Core\Domain\Model\Proxy\Roster;

use Obblm\Core\Domain\Contracts\OptionableInterface;
use Obblm\Core\Domain\Contracts\Rule\PositionInterface;
use Obblm\Core\Domain\Contracts\Rule\RosterInterface;
use Obblm\Core\Domain\Model\Proxy\Skill\Skill;
use Obblm\Core\Domain\Model\Proxy\Traits\OptionableTrait;
use Obblm\Core\Domain\Model\Proxy\Traits\TranslatableTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractPosition implements PositionInterface, OptionableInterface
{
    use TranslatableTrait;
    use OptionableTrait;
    /** @var string */
    private $key;
    /** @var int */
    private $cost;
    /** @var int */
    private $min;
    /** @var int */
    private $max;
    /** @var bool */
    private $isJourneyman;
    /** @var array */
    private $characteristics;
    /** @var Skill[] */
    private $skills = [];
    /** @var array */
    private $availableSkills;
    /** @var array */
    private $availableSkillsOnDouble;
    /** @var roster */
    private $roster;

    protected function hydrateWithOptions()
    {
        $this->setKey($this->options['key'])
            ->setName($this->options['name'])
            ->setTranslationDomain($this->options['translation_domain'])
            ->setCharacteristics($this->options['characteristics'])
            ->setCost($this->options['cost'])
            ->setMin($this->options['min'])
            ->setMax($this->options['max'])
            ->setIsJourneyman($this->options['is_journeyman'])
            ->setSkills($this->options['skills'])
            ->setAvailableSkills($this->options['available_skills'])
            ->setAvailableSkillsOnDouble($this->options['available_skills_on_double'])
            ;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function getCost(): int
    {
        return $this->cost;
    }

    public function isJourneyman(): bool
    {
        return $this->isJourneyman;
    }

    public function getIsJourneyman(): bool
    {
        return $this->isJourneyman;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getSkills(): array
    {
        return $this->skills;
    }

    public function getCharacteristics(): array
    {
        return $this->characteristics;
    }

    public function getAvailableSkills(): array
    {
        return $this->availableSkills;
    }

    public function getAvailableSkillsOnDouble(): array
    {
        return $this->availableSkillsOnDouble;
    }

    public function getRoster(): RosterInterface
    {
        return $this->roster;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function setRoster(RosterInterface $roster): self
    {
        $this->roster = $roster;

        return $this;
    }

    public function setCost(int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function setMin(int $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function setMax(int $max): self
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @param bool $isJouneyman
     */
    public function setIsJourneyman(bool $isJourneyman): self
    {
        $this->isJourneyman = $isJourneyman;

        return $this;
    }

    /**
     * @param array
     */
    public function setCharacteristics(array $characteristics): self
    {
        $this->characteristics = $characteristics;

        return $this;
    }

    public function setSkills(array $skills): self
    {
        $this->skills = $skills;

        return $this;
    }

    public function setAvailableSkills(array $availableSkills): self
    {
        $this->availableSkills = $availableSkills;

        return $this;
    }

    public function setAvailableSkillsOnDouble(array $availableSkillsOnDouble): self
    {
        $this->availableSkillsOnDouble = $availableSkillsOnDouble;

        return $this;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'key' => null,
                'name' => null,
                'translation_domain' => null,
                'characteristics' => null,
                'skills' => [],
                'available_skills' => [],
                'available_skills_on_double' => [],
                'min' => 1,
                'max' => 1,
                'cost' => null,
                'is_journeyman' => false,
            ])
            ->setRequired(['key', 'name', 'translation_domain', 'characteristics'])
            ->setAllowedTypes('key', ['string'])
            ->setAllowedTypes('name', ['string'])
            ->setAllowedTypes('translation_domain', ['string'])
            ->setAllowedTypes('characteristics', ['array'])
            ->setAllowedTypes('skills', ['array'])
            ->setAllowedTypes('available_skills', ['array'])
            ->setAllowedTypes('available_skills_on_double', ['array'])
            ->setAllowedTypes('min', ['int'])
            ->setAllowedTypes('max', ['int'])
            ->setAllowedTypes('cost', ['int'])
            ->setAllowedTypes('is_journeyman', ['bool'])
        ;
    }
}
