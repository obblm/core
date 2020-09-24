<?php

namespace Obblm\Core\Service\Rule;

use Obblm\Core\Contracts\RuleHelperInterface;
use Obblm\Core\Form\Player\ActionBb2020Type;
use Obblm\Core\Helper\Rule\AbstractRuleHelper;

class Bb2020Rule extends AbstractRuleHelper implements RuleHelperInterface
{
    public function getActionsFormClass(): string
    {
        return ActionBb2020Type::class;
    }

    public function getKey(): string
    {
        return 'bb2020';
    }
}
