<?php

namespace Obblm\Core\MessageHandler;

use Obblm\Core\Message\EmailMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EmailHandler implements MessageHandlerInterface
{
    protected $mailer;
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    public function __invoke(EmailMessage $message)
    {
        $email = $message->getContent();
        $this->mailer->send($email);
    }
}
