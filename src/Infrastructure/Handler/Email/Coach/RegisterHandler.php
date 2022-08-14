<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Handler\Email\Coach;

use Obblm\Core\Application\Notification\Coach\SendRegistrationMail;
use Obblm\Core\Infrastructure\Handler\Email\MailerHandler;

class RegisterHandler extends MailerHandler
{
    public function __invoke(SendRegistrationMail $notification)
    {
        $email = $notification->getContent();
        $this->sendMail($email);
    }
}
