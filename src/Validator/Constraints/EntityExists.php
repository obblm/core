<?php

namespace Obblm\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class EntityExists extends Constraint
{
    public $notExistMessage = 'obblm.constraints.entity.exists.violation';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
