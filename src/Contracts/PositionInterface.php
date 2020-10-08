<?php

namespace Obblm\Core\Contracts;

use Obblm\Core\Helper\Rule\Translatable;

interface PositionInterface extends Translatable
{
    public function getKey(): string;
    public function getName(): string;
    public function getCost(): int;
    public function getMax(): int;
    public function getMin(): int;
    public function getCharacteristics(): array;
    public function getSkills(): array;
    public function isJourneyMan(): bool;
}
