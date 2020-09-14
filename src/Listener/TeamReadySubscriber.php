<?php

namespace Obblm\Core\Listener;

use Obblm\Core\Event\TeamEvent;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Helper\TeamHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TeamReadySubscriber implements EventSubscriberInterface {

    private $teamHelper;
    private $ruleHelper;

    public function __construct(TeamHelper $teamHelper, RuleHelper $ruleHelper) {
        $this->teamHelper = $teamHelper;
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