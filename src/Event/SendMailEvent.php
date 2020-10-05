<?php

namespace Obblm\Core\Event;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\Event;

class SendMailEvent extends Event
{
    public const NAME = 'email.send';

    private $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function getEmail():Email
    {
        return $this->email;
    }
}
