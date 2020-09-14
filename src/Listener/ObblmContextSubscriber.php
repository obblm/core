<?php

namespace Obblm\Core\Listener;

use Obblm\Championship\Entity\Championship;
use Obblm\Core\Entity\Team;
use Obblm\Core\Service\ObblmContextualizer;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Service\TeamService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ObblmContextSubscriber implements EventSubscriberInterface
{
    protected $context;
    protected $ruleHelper;

    public function __construct(ObblmContextualizer $context, RuleHelper $ruleHelper) {
        $this->context = $context;
        $this->ruleHelper = $ruleHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onFinishRequest'
        ];
    }

    public function onFinishRequest(ControllerArgumentsEvent $event) {
        $request = $event->getRequest();
        $rule_set = false;
        if($team = $request->get('team')) {
            if($team instanceof Team) {
                $this->context->setTeam($team);
                if(!$rule_set) {
                    $this->context->setRule($this->ruleHelper->getHelper(TeamService::getTeamRule($team)));
                }
            }
        }
    }
}