<?php

namespace Obblm\Core\Listener;

use Obblm\Core\Event\TeamVersionEvent;
use Obblm\Core\Helper\RuleHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PlayerVersionEntitySubscriber implements EventSubscriberInterface
{
    private $ruleHelper;

    public function __construct(RuleHelper $ruleHelper)
    {
        $this->ruleHelper = $ruleHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            TeamVersionEvent::PRE_SAVE => 'prePersist'
        ];
    }

    public function prePersist(TeamVersionEvent $event)
    {
        $version = $event->getTeamVersion();
        $helper = $this->ruleHelper->getHelper($version->getTeam()->getRule());
        $version->setTr($helper->calculateTeamRate($version));
    }
}
