<?php

namespace Obblm\Core\Contracts;

use Obblm\Core\Helper\Rule\Translatable;

interface RosterInterface extends Translatable
{
    public function getKey(): string;
    public function getName(): string;
    public function getPositionClass(): string;
    /** @return PositionInterface[] */
    public function getPositions(): ?array;
    public function getPosition($key): ?PositionInterface;
    public function getRerollCost(): int;
    public function getInducementOptions(): ?array;
    public function canHaveApothecary(): bool;
}
