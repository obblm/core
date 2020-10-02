<?php

namespace Obblm\Core\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Obblm\Core\Entity\Rule;

trait RuleTrait
{
    /**
     * @ORM\ManyToOne(targetEntity=Rule::class, inversedBy="teams")
     */
    private $rule;

    public function getRule(): ?Rule
    {
        return $this->rule;
    }

    public function setRule(?Rule $rule): self
    {
        $this->rule = $rule;

        return $this;
    }
}
