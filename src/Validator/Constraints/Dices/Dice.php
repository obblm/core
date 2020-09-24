<?php

namespace Obblm\Core\Validator\Constraints\Dices;

use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\RangeValidator;

class Dice extends Range
{
    public $notInRangeMessage = 'obblm.constraints.dice.limit.violation';

    public $min = 1;
    public $max = 6;

    public function validatedBy()
    {
        return RangeValidator::class;
    }
}
