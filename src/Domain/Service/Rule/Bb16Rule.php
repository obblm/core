<?php

namespace Obblm\Core\Domain\Service\Rule;

use Obblm\Core\Domain\Contracts\RuleHelperInterface;

class Bb16Rule extends AbstractRuleHelper implements RuleHelperInterface
{
    public function getKey(): string
    {
        return 'bb16';
    }
}
