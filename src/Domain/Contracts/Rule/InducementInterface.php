<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Contracts\Rule;

use Obblm\Core\Domain\Model\AsValueInterface;
use Obblm\Core\Domain\Model\Proxy\Inducement\InducementType;
use Obblm\Core\Domain\Model\Translatable;

interface InducementInterface extends Translatable, AsValueInterface
{
    public function getKey(): string;

    public function getName(): string;

    public function getType(): ?InducementType;

    public function getTypeKey(): ?string;

    public function getTypeName(): string;

    public function getDiscountValue(): int;

    public function getMax(): int;

    public function getRosters(): ?array;

    public function isMultiple(): bool;
}
