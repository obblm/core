<?php

namespace Obblm\Core\Helper\Rule\Inducement;

use Obblm\Core\Contracts\InducementInterface;

class StarPlayer extends AbstractInducement implements InducementInterface
{
    /** @var array $characteristics */
    protected $characteristics;
    /** @var array $skills */
    protected $skills;

    public function __construct(array $options = [])
    {
        parent::__construct($options);
        if (isset($options['characteristics']) && $options['characteristics']) {
            $this->setCharacteristics($options['characteristics']);
        }
        if (isset($options['skills']) && $options['skills']) {
            $this->setSkills($options['skills']);
        }
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
}
