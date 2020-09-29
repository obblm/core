<?php

namespace Obblm\Core\Contracts;

use Obblm\Core\Helper\Rule\Inducement\InducementType;
use Obblm\Core\Helper\Rule\Translatable;

interface InducementInterface extends Translatable
{
    public function getKey(): string;

    public function getName(): string;

    public function getType(): ?InducementType;

    public function getTypeName(): string;

    public function getValue(): int;

    public function getDiscountValue(): int;

    public function getMax(): int;

    public function getRosters(): ?array;

    public function isMultiple(): bool;
}
