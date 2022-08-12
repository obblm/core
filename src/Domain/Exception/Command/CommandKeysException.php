<?php

namespace Obblm\Core\Domain\Exception\Command;

use Obblm\Core\Domain\Contracts\ExceptionInterface;

class CommandKeysException extends \InvalidArgumentException implements ExceptionInterface
{
    public const MESSAGE = 'You must define command keys in class "%s"';

    public function __construct(string $className)
    {
        parent::__construct(sprintf(self::MESSAGE, $className));
    }
}
