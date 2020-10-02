<?php

namespace Obblm\Core\Helper\Rule;

use Obblm\Core\Entity\Rule;

interface CanHaveRuleInterface
{
    public function getRule():?Rule;
}
