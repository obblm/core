<?php

namespace Obblm\Core\Event;

use Obblm\Core\Entity\Coach;
use Symfony\Contracts\EventDispatcher\Event;

class RegisterCoachEvent extends Event
{
    public const NAME = 'coach.register';

    protected $coach;

    public function __construct(Coach $coach)
    {
        $this->coach = $coach;
    }

    public function getCoach()
    {
        return $this->coach;
    }
}
