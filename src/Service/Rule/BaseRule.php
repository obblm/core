<?php

namespace Obblm\Core\Service\Rule;

use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Helper\Rule\AbstractRuleHelper;

class BaseRule extends AbstractRuleHelper implements RuleHelperInterface
{
    public function getKey(): string
    {
        return 'lrb6';
    }
}
