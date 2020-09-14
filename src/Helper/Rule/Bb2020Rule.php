<?php

namespace Obblm\Core\Helper\Rule;

use Obblm\Core\Form\Encounter\ActionBb2020Type;

class Bb2020Rule extends AbstractRuleHelper {
    public function getActionsFormClass(): string
    {
        return ActionBb2020Type::class;
    }
    public function getKey(): string
    {
        return 'bb2020';
    }
}
