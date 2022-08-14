<?php

namespace Obblm\Core\Resources;

use Obblm\Core\Helper\Rule\Inducement\Inducement;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InducementsAmountValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof InducementsAmount) {
            throw new UnexpectedTypeException($constraint, InducementsAmount::class);
        }
        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        $spend = 0;

        foreach ($value as $inducement) {
            /* @var Inducement $inducement */
            $spend += $inducement->getValue();
        }

        if ($spend > $constraint->budget) {
            $this->context->buildViolation($constraint->limitMessage)
                ->setParameter('{{ budget }}', $constraint->budgetToDisplay)
                ->addViolation();
        }
    }
}
