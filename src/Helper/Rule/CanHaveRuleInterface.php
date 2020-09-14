<?php

namespace Obblm\Core\Helper\Rule;

use Obblm\Core\Entity\Rule;

interface CanHaveRuleInterface {
    public function __toString():?string;
    public function getRule():?Rule;
}