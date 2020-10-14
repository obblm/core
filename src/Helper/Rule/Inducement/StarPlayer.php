<?php

namespace Obblm\Core\Helper\Rule\Inducement;

use Obblm\Core\Contracts\InducementInterface;
use Obblm\Core\Contracts\PositionInterface;
use Obblm\Core\Contracts\SkillInterface;
use Obblm\Core\Exception\NotFoundKeyException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StarPlayer extends AbstractInducement implements PositionInterface
{
    /** @var array $characteristics */
    protected $characteristics;
    /** @var array $skills */
    protected $skills = [];

    protected function hydrateWithOptions()
    {
        parent::hydrateWithOptions();
        $this->setSkills($this->options['skills']);
        $this->setCharacteristics($this->options['characteristics']);
    }

    /**
     * @return array
     */
    public function getCharacteristics(): array
    {
        return $this->characteristics;
    }

    /**
     * @param array|null $characteristics
     * @return $this
     */
    public function setCharacteristics(?array $characteristics): self
    {
        $this->characteristics = $characteristics;
        return $this;
    }

    /**
     * @return array
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * @param array|null $skills
     * @return $this
     */
    public function setSkills(?array $skills): self
    {
        $this->skills = $skills;
        return $this;
    }

    public function getCost(): int
    {
        return $this->getValue();
    }

    public function getMin(): int
    {
        return 0;
    }

    public function getOption(string $key)
    {
        if (!isset($this->options[$key])) {
            return null;
        }
        return $this->options[$key];
    }

    public function isJourneyMan(): bool
    {
        return false;
    }

    public function configureOptions(OptionsResolver $resolver):void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'characteristics' => [],
            'skills'          => [],
        ])
            ->setRequired(['characteristics'])
            ->setAllowedTypes('characteristics', ['array'])
            ->setAllowedTypes('skills', ['array'])
        ;
    }
}
