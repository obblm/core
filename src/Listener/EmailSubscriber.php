<?php

namespace Obblm\Core\Listener;

use Obblm\Core\Event\ActivateCoachEvent;
use Obblm\Core\Event\RegisterCoachEvent;
use Obblm\Core\Event\ResetPasswordCoachEvent;
use Obblm\Core\Event\SendMailEvent;
use Obblm\Core\Message\EmailMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Address;

class EmailSubscriber implements EventSubscriberInterface
{
    protected $bus;
    protected $dispatcher;
    protected $senderMail;
    protected $senderName;
    protected $defaultSender;

    public function __construct(MessageBusInterface $bus, EventDispatcherInterface $dispatcher, $senderMail, $senderName)
    {
        $this->bus = $bus;
        $this->dispatcher = $dispatcher;
        $this->senderMail = $senderMail;
        $this->senderName = $senderName;
        $this->defaultSender = new Address($this->senderMail, $this->senderName);
    }

    public static function getSubscribedEvents()
    {
        return [
            RegisterCoachEvent::NAME => 'onCoachRegistred',
            ActivateCoachEvent::NAME => 'onCoachActivated',
            ResetPasswordCoachEvent::NAME => 'onResetPassword',
            SendMailEvent::NAME => 'onSendMail',
        ];
    }

    public function onCoachRegistred(RegisterCoachEvent $event)
    {
        $coach = $event->getCoach();
        $address = new Address($coach->getEmail(), $coach->getUsername());
        $email = (new TemplatedEmail())
            ->to($address)
            ->subject('Welcome')
            ->htmlTemplate('@ObblmCore/emails/coach/register.html.twig')
            ->textTemplate('@ObblmCore/emails/coach/register.text.twig')
            ->context([
                'coach' => $coach,
            ]);
        $this->dispatcher->dispatch(new SendMailEvent($email), SendMailEvent::NAME);
    }

    public function onCoachActivated(ActivateCoachEvent $event)
    {
        $coach = $event->getCoach();
        $address = new Address($coach->getEmail(), $coach->getUsername());
        $email = (new TemplatedEmail())
            ->to($address)
            ->subject('Activation complete')
            ->htmlTemplate('@ObblmCore/emails/coach/activation.html.twig')
            ->textTemplate('@ObblmCore/emails/coach/activation.text.twig')
            ->context([
                'coach' => $coach,
            ]);
        $this->dispatcher->dispatch(new SendMailEvent($email), SendMailEvent::NAME);
    }

    public function onResetPassword(ResetPasswordCoachEvent $event)
    {
        $coach = $event->getCoach();
        $address = new Address($coach->getEmail(), $coach->getUsername());
        $email = (new TemplatedEmail())
            ->to($address)
            ->subject('Reset your password')
            ->htmlTemplate('@ObblmCore/emails/coach/reset.html.twig')
            ->textTemplate('@ObblmCore/emails/coach/reset.text.twig')
            ->context([
                'coach' => $coach,
            ]);
        $this->dispatcher->dispatch(new SendMailEvent($email), SendMailEvent::NAME);
    }

    public function onSendMail(SendMailEvent $event)
    {
        $email = $event->getEmail();
        $email->from($this->defaultSender);
        $this->bus->dispatch(new EmailMessage($email));
    }
}
