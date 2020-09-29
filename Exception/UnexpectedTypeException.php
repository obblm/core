<?php

namespace Obblm\Core\Exception;

class UnexpectedTypeException extends InvalidArgumentException
{
    public function __construct($value, string $expectedType)
    {
        parent::__construct(sprintf('Expected argument of type "%s", "%s" given', $expectedType, get_debug_type($value)));
    }
}
