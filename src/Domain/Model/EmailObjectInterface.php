<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model;

interface EmailObjectInterface
{
    public function getEmail(): ?string;
}
