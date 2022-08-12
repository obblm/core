<?php

namespace Obblm\Core\Domain\Exception\Command;

use Obblm\Core\Domain\Contracts\ExceptionInterface;

class CreateCommandException extends \LogicException implements ExceptionInterface
{
    public const MESSAGE = 'Enable to create command "%s"';

    public function __construct(string $className)
    {
        parent::__construct(sprintf(self::MESSAGE, $className));
    }
}
