<?php

namespace Obblm\Core\Listener;

use Obblm\Core\Event\TeamEvent;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Service\TeamService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TeamReadySubscriber implements EventSubscriberInterface {

    private $teamService;
    private $ruleHelper;

    public function __construct(TeamService $teamService, RuleHelper $ruleHelper) {
        $this->teamService = $teamService;
        $this->ruleHelper = $ruleHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            TeamEvent::READY => 'onReady',
            TeamEvent::NOT_READY => 'onNotReady',
        ];
    }
    public function onReady(TeamEvent $event) {
        $team = $event->getTeam();
        $helper = $this->ruleHelper->getHelper($team->getRule());
    }
    public function onNotReady(TeamEvent $event) {

    }
}