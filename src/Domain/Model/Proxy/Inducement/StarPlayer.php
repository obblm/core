<?php

namespace Obblm\Core\Domain\Model\Proxy\Inducement;

use Obblm\Core\Domain\Contracts\PositionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StarPlayer extends AbstractInducement implements PositionInterface
{
    /** @var array */
    protected $characteristics;
    /** @var array */
    protected $skills = [];

    protected function hydrateWithOptions()
    {
        parent::hydrateWithOptions();
        $this->setSkills($this->options['skills']);
        $this->setCharacteristics($this->options['characteristics']);
    }

    public function getCharacteristics(): array
    {
        return $this->characteristics;
    }

    /**
     * @return $this
     */
    public function setCharacteristics(?array $characteristics): self
    {
        $this->characteristics = $characteristics;

        return $this;
    }

    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'characteristics' => [],
            'skills' => [],
        ])
            ->setRequired(['characteristics'])
            ->setAllowedTypes('characteristics', ['array'])
            ->setAllowedTypes('skills', ['array'])
        ;
    }
}
