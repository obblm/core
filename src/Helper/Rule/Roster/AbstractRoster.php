<?php

namespace Obblm\Core\Helper\Rule\Roster;

use Obblm\Core\Contracts\PositionInterface;
use Obblm\Core\Contracts\RosterInterface;
use Obblm\Core\Exception\NotFoundKeyException;
use Obblm\Core\Helper\CoreTranslation;
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
    protected $positions = [];
    protected $special_rules;
    protected $additionalValidators;
    protected $tier;
    protected $rerollCost;
    protected $canHaveApothecary;
    protected $inducementOptions;

    protected function hydrateWithOptions()
    {
        $this->setKey($this->options['key'])
            ->setName($this->options['name'])
            ->setTranslationDomain($this->options['translation_domain'])
            ->setSpecialRules($this->options['special_rules'])
            ->setTier($this->options['tier'])
            ->setAdditionalValidators($this->options['additional_validators'])
            ->setInducementTypes($this->options['inducement_options'])
            ->setRerollCost($this->options['reroll_cost'])
            ->setCanHaveApothecary($this->options['can_have_apothecary']);
        $this->hydratePositions($this->options['player_types']);
    }

    protected function hydratePositions(array $positions): self
    {
        $positionClassName = $this->getPositionClass();
        foreach ($positions as $key => $position)
        {
            $position['key'] = $key;
            $position['name'] = CoreTranslation::getPlayerKeyType(
                $this->getTranslationDomain(),
                $this->getKey(),
                $key
            );
            $position['translation_domain'] = $this->getTranslationDomain();
            $this->positions[$key] = (new $positionClassName($position))
                                        ->setRoster($this);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPositionClass(): string
    {
        return Position::class;
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
    public function getPositions(): ?array
    {
        return $this->positions;
    }

    public function getPosition($key): ?PositionInterface
    {
        if (!isset($this->positions[$key])) {
            throw new NotFoundKeyException($key, "positions", self::class);
        }
        return $this->positions[$key];
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
     * @param array $positions
     * @return $this
     */
    public function setPositions(array $positions): self
    {
        $this->positions = $positions;
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

    /**
     * @return array
     */
    public function getSpecialRules():array
    {
        return $this->special_rules;
    }

    /**
     * @param array $special_rules
     * @return $this
     */
    public function setSpecialRules(array $special_rules): self
    {
        $this->special_rules = $special_rules;
        return $this;
    }

    /**
     * @return array
     */
    public function getAdditionalValidators(): ?array
    {
        return $this->additionalValidators;
    }

    /**
     * @param array $additionalValidators
     * @return $this
     */
    public function setAdditionalValidators(array $additionalValidators): self
    {
        $this->additionalValidators = $additionalValidators;
        return $this;
    }

    /**
     * @return int
     */
    public function getTier():int
    {
        return $this->tier;
    }

    /**
     * @param int|null $tier
     * @return $this
     */
    public function setTier(?int $tier): self
    {
        $this->tier = $tier;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'key'                   => null,
            'name'                  => null,
            'translation_domain'    => null,
            'player_types'          => [],
            'additional_validators' => [],
            'special_rules'         => [],
            'tier'                  => null,
            'inducement_options'    => self::DEFAULT_INDUCEMENT_OPTIONS,
            'reroll_cost'           => 0,
            'can_have_apothecary'   => true,
        ])
            ->setRequired('key')
            ->setRequired('name')
            ->setRequired('translation_domain')
            ->setAllowedTypes('key', ['string'])
            ->setAllowedTypes('name', ['string'])
            ->setAllowedTypes('translation_domain', ['string'])
            ->setAllowedTypes('player_types', ['array'])
            ->setAllowedTypes('special_rules', ['array'])
            ->setAllowedTypes('tier', ['int', 'null'])
            ->setAllowedTypes('additional_validators', ['array'])
            ->setAllowedTypes('inducement_options', ['array'])
            ->setAllowedTypes('reroll_cost', ['int'])
            ->setAllowedTypes('can_have_apothecary', ['bool'])
        ;
    }
}
