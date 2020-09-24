<?php

namespace Obblm\Core\Helper\Rule\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Exception\NotFoundRuleKeyExcepion;
use Obblm\Core\Exception\NoVersionException;
use Obblm\Core\Helper\PlayerHelper;
use Obblm\Core\Helper\Rule\Roster\Roster;
use Obblm\Core\Validator\Constraints\TeamValue;

/****************************
 * TEAM INFORMATION METHODS
 ***************************/
trait AbstractTeamRuleTrait
{
    public function getAvailableRosters(): ArrayCollection
    {
        return $this->getRosters();
    }

    /**
     * @return int
     */
    public function getMaxTeamCost():int
    {
        return ($this->rule['max_team_cost']) ? $this->rule['max_team_cost'] : TeamValue::LIMIT;
    }

    /**
     * @param Team $team
     * @return array
     */
    public function getTeamAvailablePlayerTypes(Team $team)
    {
        return $this->getAvailablePlayerTypes($team->getRoster());
    }

    /**
     * @param Team $team
     * @return int
     * @throws \Exception
     */
    public function getRerollCost(Team $team):int
    {
        /** @var Roster $roster */
        $roster = $this->getRosters()->get($team->getRoster());
        return (int) $roster->getRerollCost();
    }

    /**
     * @param Team $team
     * @return int
     */
    public function getApothecaryCost(Team $team):int
    {
        return (int) $this->rule['sidelines_cost']['apothecary'];
    }

    /**
     * @param Team $team
     * @return int
     */
    public function getCheerleadersCost(Team $team):int
    {
        return (int) $this->rule['sidelines_cost']['cheerleaders'];
    }

    /**
     * @param Team $team
     * @return int
     */
    public function getAssistantsCost(Team $team):int
    {
        return (int) $this->rule['sidelines_cost']['assistants'];
    }

    /**
     * @param Team $team
     * @return int
     */
    public function getPopularityCost(Team $team):int
    {
        return (int) $this->rule['sidelines_cost']['popularity'];
    }

    /**
     * @param Team $team
     * @return bool
     * @throws \Exception
     */
    public function couldHaveApothecary(Team $team):bool
    {
        /** @var Roster $roster */
        $roster = $this->getRosters()->get($team->getRoster());
        return (bool) $roster->canHaveApothecary();
    }

    public function calculateTeamRate(TeamVersion $version):?int
    {
        return $this->calculateTeamValue($version) / 10000;
    }

    public function calculateTeamValue(TeamVersion $version, bool $excludeDisposable = false):int
    {
        $team_cost = 0;
        // Players
        foreach ($version->getTeam()->getAvailablePlayers() as $basePlayer) {
            if ($basePlayer->getType()) {
                $player = (new PlayerVersion());
                try {
                    $player = PlayerHelper::getLastVersion($basePlayer);
                } catch (NoVersionException $e) { // It's a new player !
                    $basePlayer->addVersion($player);
                    $version->addPlayerVersion($player);
                    $this->setPlayerDefaultValues($player);
                }
                if (!$player->isMissingNextGame() && !($this->playerIsDisposable($player) && $excludeDisposable)) {
                    $team_cost += $player->getValue();
                }
            }
        }
        // Sidelines
        $team_cost += $version->getRerolls() * $this->getRerollCost($version->getTeam());
        $team_cost += $version->getAssistants() * $this->getAssistantsCost($version->getTeam());
        $team_cost += $version->getCheerleaders() * $this->getCheerleadersCost($version->getTeam());
        $team_cost += $version->getPopularity() * $this->getPopularityCost($version->getTeam());
        $team_cost += ($version->getApothecary()) ? $this->getApothecaryCost($version->getTeam()) : 0;

        return $team_cost;
    }

    /**
     * @return array
     */
    public function getInjuriesTable():array
    {
        return $this->getInjuries();
    }

    public function getMaxPlayersByType($roster_key, $type_key): int
    {
        /** @var Roster $roster */
        $roster = $this->getRosters()->get($roster_key);
        if (!$type = $roster->getPlayerTypes()[$type_key]) {
            throw new NotFoundRuleKeyExcepion($type_key, 'toto');
        }
        return (int) $type['max'];
    }
}
