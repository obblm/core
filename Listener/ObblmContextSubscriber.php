<?php

namespace Obblm\Core\Listener;

use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\TeamHelper;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Service\ObblmContextualizer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class ObblmContextSubscriber implements EventSubscriberInterface
{
    protected $context;
    protected $ruleHelper;
    protected $teamHelper;
    protected $session;

    public function __construct(SessionInterface $session, ObblmContextualizer $context, RuleHelper $ruleHelper, TeamHelper $teamHelper)
    {
        $this->context = $context;
        $this->ruleHelper = $ruleHelper;
        $this->session = $session;
        $this->teamHelper = $teamHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
            SecurityEvents::INTERACTIVE_LOGIN => 'setUserLocale',
            KernelEvents::CONTROLLER_ARGUMENTS => 'setContext'
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $this->onRequest($event->getRequest());
    }

    public function onRequest(Request $request)
    {
        if (!$request->hasPreviousSession()) {
            return;
        }
        // try to see if the locale has been set as a _locale routing parameter
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            // if no explicit locale has been set on this request, use one from the session
            $request->attributes->set('_locale', $request->getSession()->get('_locale', 'en'));
            $request->setLocale($request->getSession()->get('_locale', 'en'));
        }
    }

    public function setContext(ControllerArgumentsEvent $event)
    {
        $request = $event->getRequest();
        $set = false;
        if ($request->get('team')) {
            $team = $request->get('team');
            if ($team instanceof Team) {
                $this->context->setTeam($team);
                if (!$set) {
                    $this->context->setRule($this->ruleHelper->getHelper($team->getRule()));
                }
            }
        }
    }

    public function setUserLocale(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (null !== $user->getLocale()) {
            $this->session->set('_locale', $user->getLocale());
        }
    }
}
