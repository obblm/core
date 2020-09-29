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
     * @param TeamVersion $version
     * @return int
     * @throws \Exception
     */
    public function calculateTeamValue(TeamVersion $version, $excludeDisposable = false):int
    {
        return $this->getRuleHelper($version->getTeam())
            ->calculateTeamValue($version, $excludeDisposable);
    }

    /**
     * @param TeamVersion $version
     * @return int
     * @throws \Exception
     */
    public function calculateTeamRate(TeamVersion $version):int
    {
        return $this->getRuleHelper($version->getTeam())
            ->calculateTeamRate($version);
    }

    /**
     * @param Team $team
     * @return TeamVersion
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function createNewTeamVersion(Team $team):TeamVersion
    {
        return (TeamHelper::getLastVersion($team))
            ->setTreasure($this->ruleHelper->getHelper($team->getRule())->getMaxTeamCost());
    }
    public function destructTeamVersion(Team $team):TeamVersion
    {
    }

    /****************************
     * TEAM INFORMATION METHODS
     ***************************/

    /**
     * @param Team $team
     * @return int
     * @throws \Exception
     */
    public function getRerollCost(Team $team):int
    {
        return (int) $this->getRuleHelper($team)->getRerollCost($team);
    }

    /**
     * @param Team $team
     * @return int
     * @throws \Exception
     */
    public function getApothecaryCost(Team $team):int
    {
        return (int) $this->getRuleHelper($team)->getApothecaryCost($team);
    }

    /**
     * @param Team $team
     * @return int
     * @throws \Exception
     */
    public function getCheerleadersCost(Team $team):int
    {
        return (int) $this->getRuleHelper($team)->getCheerleadersCost($team);
    }

    /**
     * @param Team $team
     * @return int
     * @throws \Exception
     */
    public function getAssistantsCost(Team $team):int
    {
        return (int) $this->getRuleHelper($team)->getAssistantsCost($team);
    }

    /**
     * @param Team $team
     * @return int
     * @throws \Exception
     */
    public function getPopularityCost(Team $team):int
    {
        return (int) $this->getRuleHelper($team)->getPopularityCost($team);
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
