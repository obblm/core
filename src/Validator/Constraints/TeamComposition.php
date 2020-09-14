<?php

namespace Obblm\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use function get_class;

class TeamComposition extends Constraint {

    public $limitMessage = 'obblm.constraints.team.composition.violation';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
