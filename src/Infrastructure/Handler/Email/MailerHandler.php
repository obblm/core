<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Handler\Email;

use Obblm\Core\Domain\Contracts\DefaultSenderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

abstract class MailerHandler implements DefaultSenderInterface
{
    protected MailerInterface $mailer;
    protected Address $defaultSender;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function setDefaultSender(string $defaultSenderAddress, string $defaultSenderName)
    {
        $this->defaultSender = new Address($defaultSenderAddress, $defaultSenderName);
    }

    public function sendMail(Email $email)
    {
        $email->from($this->defaultSender);
        $this->mailer->send($email);
    }
}
