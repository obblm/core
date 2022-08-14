<?php

namespace Obblm\Core\Domain\Validator\Constraints\Team;

use function get_class;
use Symfony\Component\Validator\Constraint;

class GroupOfPositions extends Constraint
{
    public $limitMessage = 'obblm.constraints.team.group_of_positions.violation';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
