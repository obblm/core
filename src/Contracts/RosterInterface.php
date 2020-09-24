<?php

namespace Obblm\Core\Contracts;

interface RosterInterface
{
    public function getKey(): string;
    public function getTranslationKey(): string;
    public function getTranslationDomain(): string;
    public function getPlayerTypes(): ?array;
    public function getRerollCost(): int;
    public function getInducementOptions(): ?array;
    public function canHaveApothecary(): bool;
    public function __toString(): string;
}
