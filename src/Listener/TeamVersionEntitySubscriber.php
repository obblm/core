<?php

namespace Obblm\Core\Listener;

use Obblm\Core\Event\TeamVersionEvent;
use Obblm\Core\Helper\RuleHelper;
use Obblm\Core\Validator\Constraints\Team\AdditionalSkills;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TeamVersionEntitySubscriber implements EventSubscriberInterface
{
    private $ruleHelper;

    public function __construct(RuleHelper $ruleHelper)
    {
        $this->ruleHelper = $ruleHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            TeamVersionEvent::PRE_SAVE => 'prePersist',
            TeamVersionEvent::TREASURE_BASE => 'calculateTeamBaseTreasure'
        ];
    }

    public function prePersist(TeamVersionEvent $event)
    {
        $version = $event->getTeamVersion();
        $helper = $this->ruleHelper->getHelper($version->getTeam());
        $helper->applyTeamExtraCosts($version, true);
        $version->setTr($helper->calculateTeamRate($version));
    }

    public function calculateTeamBaseTreasure(TeamVersionEvent $event)
    {
        $version = $event->getTeamVersion();
        $helper = $this->ruleHelper->getHelper($version->getTeam()->getRule());
        $treasure = $helper->getMaxTeamCost($version->getTeam()) - $helper->calculateTeamValue($version);
        if ($version->getTeam()->getCreationOption('inducement_allowed') && $version->getTeam()->getCreationOption('inducements')) {
            $treasure -= $helper->calculateInducementsCost($version->getTeam()->getCreationOption('inducements'));
        }

        $version->setTreasure($treasure);
    }
}
