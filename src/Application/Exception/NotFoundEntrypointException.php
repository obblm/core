<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Exception;

use Obblm\Core\Domain\Contracts\ExceptionInterface;

class NotFoundEntrypointException extends \InvalidArgumentException implements ExceptionInterface
{
    public function __construct(string $expectedKey, string $class)
    {
        parent::__construct(sprintf('Expected entrypoint "%s" not found in "%s".', $expectedKey, $class));
    }
}
