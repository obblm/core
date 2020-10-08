<?php

namespace Obblm\Core\Validator\Constraints\Team;

use Symfony\Component\Validator\Constraint;
use function get_class;

class Composition extends Constraint
{
    public $limitMessage = 'obblm.constraints.team.composition.violation';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
