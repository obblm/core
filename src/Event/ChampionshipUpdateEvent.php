<?php

namespace Obblm\Core\Event;

use Obblm\Core\Entity\Championship;
use Symfony\Contracts\EventDispatcher\Event;

class ChampionshipUpdateEvent extends Event {
    public const NAME = 'championship.updated';

    protected $championship;

    public function __construct(Championship $championship)
    {
        $this->championship = $championship;
    }

    public function getChampionship()
    {
        return $this->championship;
    }
}