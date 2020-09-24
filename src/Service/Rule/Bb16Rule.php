<?php

namespace Obblm\Core\Service\Rule;

use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Helper\Rule\AbstractRuleHelper;

class Bb16Rule extends AbstractRuleHelper implements RuleHelperInterface
{
    public function getKey(): string
    {
        return 'bb16';
    }
}
