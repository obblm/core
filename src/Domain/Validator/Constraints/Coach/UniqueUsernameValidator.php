<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Validator\Constraints\Coach;

use Obblm\Core\Domain\Service\Coach\CoachService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUsernameValidator extends ConstraintValidator
{
    private CoachService $coachService;

    public function __construct(CoachService $coachService)
    {
        $this->coachService = $coachService;
    }

    public function validate($value, Constraint $constraint): void
    {
        $usernameExists = $this->coachService->isUsernameExists($value);

        if ($usernameExists) {
            $this->context->buildViolation(UniqueUsername::MESSAGE)
                ->addViolation();
        }
    }
}
