<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Twig;

use Obblm\Core\Domain\Model\Team;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CoreExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('roster_name', [$this, 'getRosterName']),
            new TwigFilter('tr', [$this, 'getTeamRate']),
            new TwigFilter('calculate_value', [$this, 'getTeamValue']),
        ];
    }

    public function getRosterName(Team $team)
    {
        return ''; //CoreTranslation::getRosterNameFor($team);
    }

    public function getTeamRate($value)
    {
        return $value;
    }

    public function getTeamValue($value)
    {
        return $value;
    }
}
