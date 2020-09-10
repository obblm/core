<?php

namespace Obblm\Core\Event;

use Obblm\Core\Entity\Encounter;
use Symfony\Contracts\EventDispatcher\Event;

class SendEncounterValidationMessageEvent extends Event {
    public const NAME = 'encounter.validated';

    protected $encounter;

    public function __construct(Encounter $encounter)
    {
        $this->encounter = $encounter;
    }

    public function getEncounter()
    {
        return $this->encounter;
    }
}