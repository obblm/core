<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Service;

class Hash
{
    public function __invoke(string $value): string
    {
        return hash('sha256', $value);
    }
}
