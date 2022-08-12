<?php

namespace Obblm\Core\Domain\Contracts;

use Obblm\Core\Domain\Model\Rule;

interface HasRuleInterface
{
    public function getRule(): ?Rule;
}
