<?php

namespace Obblm\Core\Helper\Rule\Roster;

use Obblm\Core\Contracts\PositionInterface;
use Obblm\Core\Contracts\RosterInterface;
use Obblm\Core\Helper\Optionable;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractPosition extends Optionable implements PositionInterface
{
    /** @var string */
    private $key;
    /** @var string */
    private $name;
    /** @var string */
    private $translationDomain;
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
    /** @var array */
    private $skills;
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
    public function getName(): string
    {
        return $this->name;
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

    /**
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @return array
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * @return array
     */
    public function getCharacteristics(): array
    {
        return $this->characteristics;
    }

    /**
     * @return array
     */
    public function getAvailableSkills(): array
    {
        return $this->availableSkills;
    }

    /**
     * @return array
     */
    public function getAvailableSkillsOnDouble(): array
    {
        return $this->availableSkillsOnDouble;
    }

    /**
     * @return RosterInterface
     */
    public function getRoster(): RosterInterface
    {
        return $this->roster;
    }


    /**
     * @return string
     */
    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
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

    /**
     * @param int $cost
     */
    public function setCost(int $cost): self
    {
        $this->cost = $cost;
        return $this;
    }

    /**
     * @param int $min
     */
    public function setMin(int $min): self
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param int $max
     */
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

    /**
     * @param array $skills
     */
    public function setSkills(array $skills): self
    {
        $this->skills = $skills;
        return $this;
    }

    /**
     * @param array $availableSkills
     */
    public function setAvailableSkills(array $availableSkills): self
    {
        $this->availableSkills = $availableSkills;
        return $this;
    }

    /**
     * @param array $availableSkillsOnDouble
     */
    public function setAvailableSkillsOnDouble(array $availableSkillsOnDouble): self
    {
        $this->availableSkillsOnDouble = $availableSkillsOnDouble;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function configureOptions(OptionsResolver $resolver):void
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
