<?php

namespace Obblm\Core\Domain\Validator\Constraints\Team;

use function get_class;
use Symfony\Component\Validator\Constraint;

class Composition extends Constraint
{
    public $limitMessage = 'obblm.constraints.team.composition.violation';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
