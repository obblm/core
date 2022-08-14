<?php

namespace Obblm\Core\Domain\Validator\Constraints\Team;

use function get_class;
use Symfony\Component\Validator\Constraint;

class Value extends Constraint
{
    const LIMIT = 1000000;

    public $limitMessage = 'obblm.constraints.team.value.violation';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
