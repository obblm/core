<?php

namespace Obblm\Core\Contracts;

use Obblm\Core\Helper\Rule\Translatable;

interface SkillInterface extends Translatable
{
    public function getKey(): string;

    public function getName(): string;

    public function getDomain(): string;

    public function getType(): string;

    public function getTypeName(): string;
}
