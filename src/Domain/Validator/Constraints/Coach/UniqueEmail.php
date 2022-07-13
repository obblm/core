<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Validator\Constraints\Coach;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueEmail extends Constraint
{
    public const MESSAGE = 'Email already used';

    public function validatedBy(): string
    {
        return UniqueEmailValidator::class;
    }
}
