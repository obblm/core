<?php

namespace Obblm\Core\Listener;

use Obblm\Core\Entity\Coach;
use Obblm\Core\Entity\EmailObjectInterface;
use Obblm\Core\Event\ActivateCoachEvent;
use Obblm\Core\Event\ChampionshipStartMessageEvent;
use Obblm\Core\Event\RegisterCoachEvent;
use Obblm\Core\Event\SendEncounterValidationMessageEvent;
use Obblm\Core\Event\SendInvitationMessageEvent;
use Obblm\Core\Message\EmailMessage;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Address;

class EmailSubscriber implements EventSubscriberInterface
{
    protected $bus;
    protected $senderMail = "noreply@obblm.com"; // TODO: change with env var
    protected $senderName = "BBLM"; // TODO: change with env var
    protected $defaultSender;

    public function __construct(MessageBusInterface $bus)
    {
        $this->bus = $bus;
        $this->defaultSender = new Address($this->senderMail, $this->senderName);
    }

    public static function getSubscribedEvents()
    {
        return [
            RegisterCoachEvent::NAME => 'onCoachRegistred',
            ActivateCoachEvent::NAME => 'onCoachActivated',
        ];
    }

    public function onCoachRegistred(RegisterCoachEvent $event)
    {
        $coach = $event->getCoach();
        $address = new Address($coach->getEmail(), $coach->getUsername());
        $email = (new TemplatedEmail())
            ->from($this->defaultSender)
            ->to($address)
            ->subject('Welcome')
            ->htmlTemplate('@ObblmCore/emails/coach/register.html.twig')
            ->textTemplate('@ObblmCore/emails/coach/register.text.twig')
            ->context([
                'coach' => $coach,
            ]);
        $this->bus->dispatch(new EmailMessage($email));
    }

    public function onCoachActivated(ActivateCoachEvent $event)
    {
        $coach = $event->getCoach();
        $address = new Address($coach->getEmail(), $coach->getUsername());
        $email = (new TemplatedEmail())
            ->from($this->defaultSender)
            ->to($address)
            ->subject('Activation complete')
            ->htmlTemplate('@ObblmCore/emails/coach/activation.html.twig')
            ->textTemplate('@ObblmCore/emails/coach/activation.text.twig')
            ->context([
                'coach' => $coach,
            ]);
        $this->bus->dispatch(new EmailMessage($email));
    }
}
