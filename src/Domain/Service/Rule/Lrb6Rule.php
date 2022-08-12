<?php

namespace Obblm\Core\Domain\Service\Rule;

use Obblm\Core\Domain\Contracts\RuleHelperInterface;

class Lrb6Rule extends AbstractRuleHelper implements RuleHelperInterface
{
    public function getKey(): string
    {
        return 'lrb6';
    }
}
