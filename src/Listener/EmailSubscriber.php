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
            SendInvitationMessageEvent::NAME => 'onSendInvitationMessage',
            RegisterCoachEvent::NAME => 'onCoachRegistred',
            ActivateCoachEvent::NAME => 'onCoachActivated',
            ChampionshipStartMessageEvent::NAME => 'onChampionshipStart',
            SendEncounterValidationMessageEvent::NAME => 'onEncounterValidated',
        ];
    }

    public function onSendInvitationMessage(SendInvitationMessageEvent $event) {
        $object = $event->getObject();

        if(!$object instanceof EmailObjectInterface) {
            throw new UnexpectedTypeException($object, EmailObjectInterface::class);
        }

        $championship = $event->getChampionship();
        if($object instanceof Coach) {
            $to = new Address($object->getEmail(), $object->getUsername());
        }
        else {
            $to = new Address($object->getEmail());
        }
        $email = (new TemplatedEmail())
            ->from( $this->default_sender )
            ->to($to)
            ->subject('Invitation')
            ->htmlTemplate('emails/championship/invitation.html.twig')
            ->textTemplate('emails/championship/invitation.text.twig')
            ->context([
                'championship' => $championship,
            ]);
        $this->bus->dispatch(new EmailMessage($email));
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
    public function onChampionshipStart(ChampionshipStartMessageEvent $event) {
        $coach = $event->getCoach();
        $championship = $event->getChampionship();
        $address = new Address($coach->getEmail(), $coach->getUsername());
        $email = (new TemplatedEmail())
            ->from( $this->default_sender )
            ->to( $address )
            ->subject('Championship started')
            ->htmlTemplate('emails/championship/start.html.twig')
            ->textTemplate('emails/championship/start.text.twig')
            ->context([
                'coach' => $coach,
                'championship' => $championship,
            ]);
        $this->bus->dispatch(new EmailMessage($email));
    }

    public function onEncounterValidated(SendEncounterValidationMessageEvent $event) {
        $encounter = $event->getEncounter();
        $validator = $encounter->getValidatedBy();

        $email = (new TemplatedEmail())
            ->from( $this->default_sender )
            ->subject('Encounter validated')
            ->htmlTemplate('emails/encounter/validate.html.twig')
            ->textTemplate('emails/encounter/validate.text.twig');

        $address = new Address($encounter->getHomeTeam()->getCoach()->getEmail(), $encounter->getHomeTeam()->getCoach()->getUsername());
        $email->to( $address )
            ->context([
                'encounter' => $encounter,
                'validator' => $validator,
                'my_team' => $encounter->getHomeTeam(),
                'against' => $encounter->getVisitorTeam()
            ]);

        $this->bus->dispatch(new EmailMessage($email));

        $address = new Address($encounter->getVisitorTeam()->getCoach()->getEmail(), $encounter->getVisitorTeam()->getCoach()->getUsername());
        $email->to( $address )
            ->context([
                'encounter' => $encounter,
                'validator' => $validator,
                'my_team' => $encounter->getVisitorTeam(),
                'against' => $encounter->getHomeTeam()
            ]);

        $this->bus->dispatch(new EmailMessage($email));
    }
}