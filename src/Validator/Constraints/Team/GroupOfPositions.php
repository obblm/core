<?php

namespace Obblm\Core\Validator\Constraints\Team;

use Symfony\Component\Validator\Constraint;
use function get_class;

class GroupOfPositions extends Constraint
{
    public $limitMessage = 'obblm.constraints.team.group_of_positions.violation';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
