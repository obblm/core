<?php

namespace Obblm\Core\Contracts;

use Obblm\Core\Helper\Rule\Inducement\InducementType;

interface InducementInterface
{
    public function getType(): ?InducementType;
    public function getKey(): string;
    public function getValue(): int;
    public function getDiscountValue(): int;
    public function getMax(): int;
    public function getRosters(): ?array;
    public function isMultiple(): bool;
    public function __toString(): string;
}
