<?php

namespace Obblm\Core\Resources;

use Symfony\Component\Validator\Constraint;

class SkillsQuantity extends Constraint
{
    public $limitMessage = 'obblm.constraints.skills.quantity.violation';
    public $limitByTypeMessage = 'obblm.constraints.skills.quantity_type.violation';
    public $limitByPlayerMessage = 'obblm.constraints.skills.quantity_player.violation';

    public $helper;
    public $context;

    public function __construct($context = ['single', 'double'], $options = null)
    {
        parent::__construct($options);
        $this->context = $context;
    }

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
