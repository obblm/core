<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Validator\Constraints\Coach;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueUsername extends Constraint
{
    public const MESSAGE = 'Username already used';

    public function validatedBy(): string
    {
        return UniqueUsernameValidator::class;
    }
}
