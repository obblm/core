<?php

namespace Obblm\Core\Contracts;

use Obblm\Core\Helper\Rule\Translatable;

interface RosterInterface extends Translatable
{
    public function getKey(): string;
    public function getName(): string;
    public function getPlayerTypes(): ?array;
    public function getRerollCost(): int;
    public function getInducementOptions(): ?array;
    public function canHaveApothecary(): bool;
}
