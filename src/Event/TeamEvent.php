<?php

namespace Obblm\Core\Event;

use Obblm\Core\Entity\Team;
use Symfony\Contracts\EventDispatcher\Event;

class TeamEvent extends Event
{
    public const READY = 'team.ready';
    public const NOT_READY = 'team.not.ready';

    protected $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function getTeam():Team
    {
        return $this->team;
    }
}
