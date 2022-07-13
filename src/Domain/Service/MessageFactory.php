<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Service;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @codeCoverageIgnore
 */
class MessageFactory
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate($value): ConstraintViolationListInterface
    {
        return $this->validator->validate($value);
    }

    /**
     * @param mixed ...$attrs
     *
     * @return object|ConstraintViolationListInterface
     */
    public function forge(string $class, ...$attrs)
    {
        $value = new $class($attrs[0], $attrs[1], $attrs[2]);
        $errors = $this->validate($value);
        if ($errors->count() > 0) {
            return $errors;
        }

        return $value;
    }
}
