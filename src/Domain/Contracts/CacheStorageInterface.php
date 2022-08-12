<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Contracts;

interface CacheStorageInterface
{
    public function getOrCreate(string $key, callable $fallback);
}
