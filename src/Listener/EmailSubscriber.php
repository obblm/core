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
    protected $sender_mail = "noreply@obblm.com"; // TODO: change with env var
    protected $sender_name = "BBLM"; // TODO: change with env var
    protected $default_sender;

    public function __construct(MessageBusInterface $bus) {
        $this->bus = $bus;
        $this->default_sender = new Address($this->sender_mail, $this->sender_name);
    }

    public static function getSubscribedEvents()
    {
        return [
            RegisterCoachEvent::NAME => 'onCoachRegistred',
            ActivateCoachEvent::NAME => 'onCoachActivated',
        ];
    }

    public function onCoachRegistred(RegisterCoachEvent $event) {
        $coach = $event->getCoach();
        $address = new Address($coach->getEmail(), $coach->getUsername());
        $email = (new TemplatedEmail())
            ->from( $this->default_sender )
            ->to( $address )
            ->subject('Welcome')
            ->htmlTemplate('emails/coach/register.html.twig')
            ->textTemplate('emails/coach/register.text.twig')
            ->context([
                'coach' => $coach,
            ]);
        $this->bus->dispatch(new EmailMessage($email));
    }

    public function onCoachActivated(ActivateCoachEvent $event) {
        $coach = $event->getCoach();
        $address = new Address($coach->getEmail(), $coach->getUsername());
        $email = (new TemplatedEmail())
            ->from( $this->default_sender )
            ->to( $address )
            ->subject('Activation complete')
            ->htmlTemplate('emails/coach/activation.html.twig')
            ->textTemplate('emails/coach/activation.text.twig')
            ->context([
                'coach' => $coach,
            ]);
        $this->bus->dispatch(new EmailMessage($email));
    }
}