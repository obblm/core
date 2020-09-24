<?php

namespace Obblm\Core\Listener;

use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\TeamHelper;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Service\ObblmContextualizer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ObblmContextSubscriber implements EventSubscriberInterface
{
    protected $context;
    protected $ruleHelper;
    protected $teamHelper;

    public function __construct(ObblmContextualizer $context, RuleHelper $ruleHelper, TeamHelper $teamHelper)
    {
        $this->context = $context;
        $this->ruleHelper = $ruleHelper;
        $this->teamHelper = $teamHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onFinishRequest'
        ];
    }

    public function onFinishRequest(ControllerArgumentsEvent $event)
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
}
