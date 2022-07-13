<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Contracts;

use Obblm\Core\Domain\Model\Translatable;

interface SkillInterface extends Translatable
{
    public function getKey(): string;

    public function getName(): string;

    public function getType(): string;

    public function getTypeName(): string;
}
