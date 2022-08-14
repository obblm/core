<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Handler\Email\Coach;

use Obblm\Core\Application\Notification\Coach\SendActivationMail;
use Obblm\Core\Infrastructure\Handler\Email\MailerHandler;

class ActivateHandler extends MailerHandler
{
    public function __invoke(SendActivationMail $notification)
    {
        $email = $notification->getContent();
        $this->sendMail($email);
    }
}
