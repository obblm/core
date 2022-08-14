<?php

namespace Obblm\Core\Domain\Model\Proxy\Roster;

use Obblm\Core\Domain\Contracts\Rule\PositionInterface;
use Obblm\Core\Domain\Contracts\Rule\RosterInterface;
use Obblm\Core\Domain\Model\Proxy\Traits\OptionableTrait;
use Obblm\Core\Domain\Model\Proxy\Traits\TranslatableTrait;
use Obblm\Core\Domain\Service\CoreTranslation;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractRoster implements RosterInterface
{
    use TranslatableTrait;
    use OptionableTrait;
    const DEFAULT_INDUCEMENT_OPTIONS = [
        'discount_bribe' => false,
        'discount_halfling_master_chef' => false,
        'extra_apothecary' => true,
        'igor' => false,
    ];

    protected $key;
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
        foreach ($positions as $key => $position) {
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

    public function getPositionClass(): string
    {
        return Position::class;
    }

    public function getKey(): string
    {
        return $this->key;
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
            throw new NotFoundKeyException($key, 'positions', self::class);
        }

        return $this->positions[$key];
    }

    public function canHaveApothecary(): bool
    {
        return $this->canHaveApothecary;
    }

    public function getCanHaveApothecary(): bool
    {
        return $this->canHaveApothecary;
    }

    public function getRerollCost(): int
    {
        return $this->rerollCost;
    }

    /**
     * @return array
     */
    public function getInducementOptions(): ?array
    {
        return $this->inducementOptions;
    }

    /**
     * @return $this
     */
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return $this
     */
    public function setPositions(array $positions): self
    {
        $this->positions = $positions;

        return $this;
    }

    /**
     * @return $this
     */
    public function setInducementTypes(array $inducementTypes): self
    {
        $this->inducementTypes = $inducementTypes;

        return $this;
    }

    /**
     * @return $this
     */
    public function setRerollCost(int $rerollCost): self
    {
        $this->rerollCost = $rerollCost;

        return $this;
    }

    /**
     * @return $this
     */
    public function setCanHaveApothecary(bool $canHaveApothecary): self
    {
        $this->canHaveApothecary = $canHaveApothecary;

        return $this;
    }

    public function getSpecialRules(): array
    {
        return $this->special_rules;
    }

    /**
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
     * @return $this
     */
    public function setAdditionalValidators(array $additionalValidators): self
    {
        $this->additionalValidators = $additionalValidators;

        return $this;
    }

    public function getTier(): int
    {
        return $this->tier;
    }

    /**
     * @return $this
     */
    public function setTier(?int $tier): self
    {
        $this->tier = $tier;

        return $this;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'key' => null,
            'name' => null,
            'translation_domain' => null,
            'player_types' => [],
            'additional_validators' => [],
            'special_rules' => [],
            'tier' => null,
            'inducement_options' => self::DEFAULT_INDUCEMENT_OPTIONS,
            'reroll_cost' => 0,
            'can_have_apothecary' => true,
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
