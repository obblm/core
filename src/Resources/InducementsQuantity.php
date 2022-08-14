<?php

namespace Obblm\Core\Resources;

use Obblm\Core\Contracts\RuleHelperInterface;
use Symfony\Component\Validator\Constraint;

class InducementsQuantity extends Constraint
{
    public $limitMessage = 'obblm.constraints.inducements.quantity.violation';

    public $helper;

    public function __construct(RuleHelperInterface $helper, $options = null)
    {
        parent::__construct($options);
        $this->helper = $helper;
    }

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
