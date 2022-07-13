<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model\Traits;

use Obblm\Core\Domain\Model\Rule;

trait RuleTrait
{
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
