<?php

namespace Obblm\Core\Exception;

class NotFoundEntrypointException extends \InvalidArgumentException implements ExceptionInterface
{
    public function __construct(string $expectedKey, string $class)
    {
        parent::__construct(sprintf('Expected entrypoint "%s" not found in "%s".', $expectedKey, $class));
    }
}
