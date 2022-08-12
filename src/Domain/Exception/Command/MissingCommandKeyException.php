<?php

namespace Obblm\Core\Domain\Exception\Command;

use Obblm\Core\Domain\Contracts\ExceptionInterface;

class MissingCommandKeyException extends \InvalidArgumentException implements ExceptionInterface
{
    public const MESSAGE = 'The command argument "%s" is missing for class "%s"';

    public function __construct(string $className, string $key)
    {
        parent::__construct(sprintf(self::MESSAGE, $key, $className));
    }
}
