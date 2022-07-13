<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model;

interface Translatable
{
    public function __toString(): string;

    public function getTranslationVars(): array;

    public function getTranslationDomain(): string;
}
