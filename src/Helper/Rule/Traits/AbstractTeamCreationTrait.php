<?php

namespace Obblm\Core\Helper\Rule\Traits;

use Obblm\Core\Form\Team\BaseTeamOptionsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

trait AbstractTeamCreationTrait
{
    abstract public function getMaxTeamCost(): int;
    public function getTeamCreationOptions(): array
    {
    }
    public function getTeamCreationForm(): string
    {
        return BaseTeamOptionsType::class;
    }
    public function getTeamCreationResolver(): array
    {
    }
}
