<?php

namespace Obblm\Core\Event;

use Obblm\Core\Entity\TeamVersion;
use Symfony\Contracts\EventDispatcher\Event;

class TeamVersionEvent extends Event
{
    public const LOCKED = 'team.version.locked';
    public const HAS_LEVEL_UP = 'team.version.has_level_up';
    public const TREASURE_BASE = 'team.version.treasure.base';
    public const PRE_SAVE = 'team.version.pre_save';

    protected $version;

    public function __construct(TeamVersion $version)
    {
        $this->version = $version;
    }

    public function getTeamVersion()
    {
        return $this->version;
    }
}
