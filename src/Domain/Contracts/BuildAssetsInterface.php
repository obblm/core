<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Contracts;

interface BuildAssetsInterface
{
    public function getPath(): string;
}
