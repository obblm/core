<?php

namespace Obblm\Core\Validator\Constraints\Team;

use Symfony\Component\Validator\Constraint;

class InducementsAmount extends Constraint
{
    public $limitMessage = 'obblm.constraints.inducements.amount.violation';

    public $budget;
    public $budgetToDisplay;

    public function __construct($budget = 0, $budgetToDisplay = null, $options = null)
    {
        parent::__construct($options);
        $this->budget = $budget;
        $this->budgetToDisplay = $budgetToDisplay ? $budgetToDisplay : $budget;
    }

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
