<?php

namespace Obblm\Core\Helper\Rule\Roster;

use Obblm\Core\Helper\Optionable;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractRoster extends Optionable
{
    const DEFAULT_INDUCEMENT_OPTIONS = [
        'discount_bribe' => false,
        'discount_halfling_master_chef' => false,
        'extra_apothecary' => true,
        'igor' => false
    ];

    protected $key;
    protected $translation_key;
    protected $translation_domain;
    protected $player_types;
    protected $reroll_cost;
    protected $can_have_apothecary;
    protected $inducement_options;

    protected function hydrateWithOptions($options)
    {
        $this->setKey($options['key'])
            ->setTranslationKey($options['translation_key'])
            ->setTranslationDomain($options['translation_domain'])
            ->setPlayerTypes($options['player_types'])
            ->setInducementTypes($options['inducement_options'])
            ->setRerollCost($options['reroll_cost'])
            ->setCanHaveApothecary($options['can_have_apothecary']);
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
    public function getTranslationKey(): string
    {
        return $this->translation_key;
    }

    /**
     * @return string
     */
    public function getTranslationDomain(): string
    {
        return $this->translation_domain;
    }

    /**
     * @return array
     */
    public function getPlayerTypes(): ?array
    {
        return $this->player_types;
    }

    /**
     * @return bool
     */
    public function canHaveApothecary():bool
    {
        return $this->can_have_apothecary;
    }

    /**
     * @return bool
     */
    public function getCanHaveApothecary():bool
    {
        return $this->can_have_apothecary;
    }

    /**
     * @return int
     */
    public function getRerollCost():int
    {
        return $this->reroll_cost;
    }

    /**
     * @return array
     */
    public function getInducementOptions():?array
    {
        return $this->inducement_options;
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
     * @param string $translation_key
     * @return $this
     */
    public function setTranslationKey(string $translation_key): self
    {
        $this->translation_key = $translation_key;
        return $this;
    }

    /**
     * @param string $translation_domain
     * @return $this
     */
    public function setTranslationDomain(string $translation_domain): self
    {
        $this->translation_domain = $translation_domain;
        return $this;
    }

    /**
     * @param array $player_types
     * @return $this
     */
    public function setPlayerTypes(array $player_types): self
    {
        $this->player_types = $player_types;
        return $this;
    }

    /**
     * @param array $inducement_types
     * @return $this
     */
    public function setInducementTypes(array $inducement_types): self
    {
        $this->inducement_types = $inducement_types;
        return $this;
    }

    /**
     * @param int $reroll_cost
     * @return $this
     */
    public function setRerollCost(int $reroll_cost): self
    {
        $this->reroll_cost = $reroll_cost;
        return $this;
    }

    /**
     * @param bool $can_have_apothecary
     * @return $this
     */
    public function setCanHaveApothecary(bool $can_have_apothecary): self
    {
        $this->can_have_apothecary = $can_have_apothecary;
        return $this;
    }

    public function __toString(): string
    {
        return $this->translation_key;
    }

    public function resolveOptions($options)
    {
        parent::resolveOptions($options);
        $this->hydrateWithOptions($options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'key'                 => null,
            'translation_key'     => null,
            'translation_domain'  => null,
            'player_types'        => [],
            'inducement_options'  => [],
            'reroll_cost'         => 0,
            'can_have_apothecary' => true,
        ])
            ->setRequired('key')
            ->setRequired('translation_key')
            ->setRequired('translation_domain')
            ->setAllowedTypes('key', ['string'])
            ->setAllowedTypes('translation_key', ['string'])
            ->setAllowedTypes('translation_domain', ['string'])
            ->setAllowedTypes('player_types', ['array'])
            ->setAllowedTypes('inducement_options', ['array'])
            ->setAllowedTypes('reroll_cost', ['int'])
            ->setAllowedTypes('can_have_apothecary', ['bool'])
        ;
    }
}
