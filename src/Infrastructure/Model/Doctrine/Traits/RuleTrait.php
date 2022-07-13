<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Model\Doctrine\Traits;

use Doctrine\ORM\Mapping as ORM;
use Obblm\Core\Infrastructure\Model\Doctrine\Rule;

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
