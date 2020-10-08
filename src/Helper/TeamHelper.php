<?php

namespace Obblm\Core\Helper;

use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Contracts\RuleHelperInterface;

/**
 * Class TeamHelper
 * @package Obblm\Core\Helper
 */
class TeamHelper
{
    private $ruleHelper;

    public function __construct(RuleHelper $ruleHelper)
    {
        $this->ruleHelper = $ruleHelper;
    }

    /**
     * @param Team $team
     * @return RuleHelperInterface
     * @throws \Exception
     */
    public function getRuleHelper(Team $team):RuleHelperInterface
    {
        if (!$team->getRule()) {
            throw new \Exception('This team does not have a rule');
        }
        return $this->ruleHelper->getHelper($team->getRule());
    }

    public static function getLastVersion(Team  $team):TeamVersion
    {
        $versions = $team->getVersions();
        /** @var TeamVersion $last */
        $last = $versions->first();
        if ($last) {
            return $last;
        }
        $version = new TeamVersion();
        $team->addVersion($version);
        return $version;
    }

    /**********************
     * TEAM HELPER METHODS
     **********************/

    /**
     * @param Team $team
     * @return TeamVersion
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function createNewTeamVersion(Team $team):TeamVersion
    {
        $treasure = $this->getNewTeamVersionTreasure($team);

        return (TeamHelper::getLastVersion($team))
            ->setTreasure($treasure);
    }
    public function getNewTeamVersionTreasure(Team $team):int
    {
        if($team->getVersions()->count() > 0) {
            return TeamHelper::getLastVersion($team)->getTreasure();
        }
        return $this->getTeamBaseTreasure($team);
    }
    public function getTeamBaseTreasure(Team $team):int
    {
        $options = $team->getCreationOptions();
        if(isset($options['max_team_cost'])) {
            return $options['max_team_cost'];
        }
        return $this->ruleHelper->getHelper($team)->getMaxTeamCost();
    }
    public function destructTeamVersion(Team $team):TeamVersion
    {
    }

    /**
     * @param Team $team
     * @return bool
     * @throws \Exception
     */
    public function couldHaveApothecary(Team $team):bool
    {
        if (!$team->getRule()) {
            throw new \Exception('This team does not have a rule');
        }
        return (bool) $this->getRuleHelper($team)->couldHaveApothecary($team);
    }
}
