<?php

namespace Obblm\Core\Validator\Constraints;

use Obblm\Core\Entity\Championship;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ParticipateToChampionshipValidator extends ConstraintValidator {

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ParticipateToChampionship) {
            throw new UnexpectedTypeException($constraint, ParticipateToChampionship::class);
        }
        if($value !== null) {
            if (!$value instanceof Championship) {
                throw new UnexpectedTypeException($value, Championship::class);
            }
            if($value->getMaxTeams() <= $value->getTeams()->count()) {
                $this->context->buildViolation($constraint->limitMessage)
                    ->setParameter('{{ limit }}', $value->getMaxTeams())
                    ->addViolation();
            }
            if($value->getIsLocked()) {
                $this->context->buildViolation($constraint->startedMessage)
                    ->addViolation();
            }
        }
    }
}
