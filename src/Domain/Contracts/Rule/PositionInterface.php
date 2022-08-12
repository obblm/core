<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Contracts\Rule;

use Obblm\Core\Domain\Model\Translatable;

interface PositionInterface extends Translatable
{
    public function getKey(): string;

    public function getName(): string;

    public function getCost(): int;

    public function getMax(): int;

    public function getMin(): int;

    public function getOption(string $key);

    public function getCharacteristics(): array;

    public function getSkills(): array;

    public function isJourneyMan(): bool;
}
