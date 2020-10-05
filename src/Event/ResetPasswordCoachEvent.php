<?php

namespace Obblm\Core\Event;

use Obblm\Core\Entity\Coach;
use Symfony\Contracts\EventDispatcher\Event;

class ResetPasswordCoachEvent extends Event
{
    public const NAME = 'coach.password.reset';

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
