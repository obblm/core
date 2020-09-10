<?php

namespace Obblm\Core\Service\Rule;

use Obblm\Core\Form\Encounter\ActionBb2020Type;

class Bb2020Rule extends AbstractRule {
    public function getActionsFormClass(): string
    {
        return ActionBb2020Type::class;
    }
    public function getKey(): string
    {
        return 'bb2020';
    }
}
