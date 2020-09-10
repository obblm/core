<?php

namespace Obblm\Core\Validator\Constraints;

use Obblm\Core\Entity\Encounter;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EncounterDifferentTeamsValidator extends ConstraintValidator {

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof EncounterDifferentTeams) {
            throw new UnexpectedTypeException($constraint, EncounterDifferentTeams::class);
        }
        if (!$value instanceof Encounter) {
            throw new UnexpectedTypeException($value, Encounter::class);
        }
        if($value->getHomeTeam() == $value->getVisitorTeam()) {
            $this->context->buildViolation($constraint->limitMessage)
                ->addViolation();
        }
    }
}
