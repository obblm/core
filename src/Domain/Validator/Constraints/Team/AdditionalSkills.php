<?php

namespace Obblm\Core\Domain\Validator\Constraints\Team;

use Symfony\Component\Validator\Constraint;

class AdditionalSkills extends Constraint
{
    const NONE = 0;
    const FREE = 1;
    const NOT_FREE = 2;

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
