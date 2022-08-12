<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Contracts\Rule;

use Obblm\Core\Domain\Contracts\OptionableInterface;
use Obblm\Core\Domain\Model\Translatable;

interface RosterInterface extends Translatable, OptionableInterface
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
