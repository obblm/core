<?php

namespace Obblm\Core\Event;

use Obblm\Core\Entity\Encounter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class EncounterValidatedEvent extends Event {
    public const NAME = 'encounter.validation';

    protected $encounter;
    protected $validator;

    public function __construct(Encounter $encounter, UserInterface $validator = null)
    {
        $this->encounter = $encounter;
        $this->validator = $validator;
    }

    public function getEncounter()
    {
        return $this->encounter;
    }

    public function getValidator()
    {
        return $this->validator;
    }
}