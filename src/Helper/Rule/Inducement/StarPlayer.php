<?php

namespace Obblm\Core\Helper\Rule\Inducement;

use Obblm\Core\Contracts\InducementInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StarPlayer extends AbstractInducement implements InducementInterface
{
    /** @var array $characteristics */
    protected $characteristics;
    /** @var array $skills */
    protected $skills;

    protected function hydrateWithOptions()
    {
        parent::hydrateWithOptions();
        $this->characteristics = $this->options['characteristics'];
        $this->skills = $this->options['skills'];
    }

    /**
     * @return array
     */
    public function getCharacteristics(): ?array
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
    public function getSkills(): ?array
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

    public function configureOptions(OptionsResolver $resolver):void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'characteristics' => null,
            'skills'          => null,
        ])
            ->setRequired(['characteristics'])
            ->setAllowedTypes('characteristics', ['array', 'null'])
            ->setAllowedTypes('skills', ['array', 'null'])
        ;
    }
}
