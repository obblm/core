<?php

namespace Obblm\Core\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use function get_class;

class ParticipateToChampionship extends Constraint {

    public $limitMessage = 'obblm.constraints.team.championship.teams_limit.violation';
    public $closedMessage = 'obblm.constraints.team.championship.closed.violation';
    public $startedMessage = 'obblm.constraints.team.championship.started.violation';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}
