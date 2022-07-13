<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Exception;

use Obblm\Core\Domain\Contracts\ExceptionInterface;

class NotFoundKeyException extends \InvalidArgumentException implements ExceptionInterface
{
    public function __construct(string $expectedKey, string $property, string $type)
    {
        parent::__construct(sprintf('Expected key "%s" not found in "%s" property in %s.', $expectedKey, $property, $type));
    }
}
