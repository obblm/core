<?php

namespace Obblm\Core\Helper\Rule\Roster;

use Obblm\Core\Contracts\RosterInterface;
use Obblm\Core\Helper\Optionable;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractRoster extends Optionable implements RosterInterface
{
    const DEFAULT_INDUCEMENT_OPTIONS = [
        'discount_bribe' => false,
        'discount_halfling_master_chef' => false,
        'extra_apothecary' => true,
        'igor' => false
    ];

    protected $key;
    protected $name;
    protected $translationDomain;
    protected $playerTypes;
    protected $rerollCost;
    protected $canHaveApothecary;
    protected $inducementOptions;

    protected function hydrateWithOptions()
    {
        $this->setKey($this->options['key'])
            ->setName($this->options['name'])
            ->setTranslationDomain($this->options['translation_domain'])
            ->setPlayerTypes($this->options['player_types'])
            ->setInducementTypes($this->options['inducement_options'])
            ->setRerollCost($this->options['reroll_cost'])
            ->setCanHaveApothecary($this->options['can_have_apothecary']);
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

    /**
     * @return string
     */
    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    /**
     * @return array
     */
    public function getPlayerTypes(): ?array
    {
        return $this->playerTypes;
    }

    /**
     * @return bool
     */
    public function canHaveApothecary():bool
    {
        return $this->canHaveApothecary;
    }

    /**
     * @return bool
     */
    public function getCanHaveApothecary():bool
    {
        return $this->canHaveApothecary;
    }

    /**
     * @return int
     */
    public function getRerollCost():int
    {
        return $this->rerollCost;
    }

    /**
     * @return array
     */
    public function getInducementOptions():?array
    {
        return $this->inducementOptions;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $translationDomain
     * @return $this
     */
    public function setTranslationDomain(string $translationDomain): self
    {
        $this->translationDomain = $translationDomain;
        return $this;
    }

    /**
     * @param array $playerTypes
     * @return $this
     */
    public function setPlayerTypes(array $playerTypes): self
    {
        $this->playerTypes = $playerTypes;
        return $this;
    }

    /**
     * @param array $inducementTypes
     * @return $this
     */
    public function setInducementTypes(array $inducementTypes): self
    {
        $this->inducementTypes = $inducementTypes;
        return $this;
    }

    /**
     * @param int $rerollCost
     * @return $this
     */
    public function setRerollCost(int $rerollCost): self
    {
        $this->rerollCost = $rerollCost;
        return $this;
    }

    /**
     * @param bool $canHaveApothecary
     * @return $this
     */
    public function setCanHaveApothecary(bool $canHaveApothecary): self
    {
        $this->canHaveApothecary = $canHaveApothecary;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'key'                 => null,
            'name'                => null,
            'translation_domain'  => null,
            'player_types'        => [],
            'inducement_options'  => [],
            'reroll_cost'         => 0,
            'can_have_apothecary' => true,
        ])
            ->setRequired('key')
            ->setRequired('name')
            ->setRequired('translation_domain')
            ->setAllowedTypes('key', ['string'])
            ->setAllowedTypes('name', ['string'])
            ->setAllowedTypes('translation_domain', ['string'])
            ->setAllowedTypes('player_types', ['array'])
            ->setAllowedTypes('inducement_options', ['array'])
            ->setAllowedTypes('reroll_cost', ['int'])
            ->setAllowedTypes('can_have_apothecary', ['bool'])
        ;
    }
}
