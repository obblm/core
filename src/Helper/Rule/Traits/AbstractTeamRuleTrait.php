<?php

namespace Obblm\Core\Helper\Rule\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Exception\InvalidArgumentException;
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
        if (!$version->getTeam()) {
            throw new InvalidArgumentException();
        }
        $value = 0;
        // Players
        foreach ($version->getTeam()->getAvailablePlayers() as $player) {
            if ($player->getType()) {
                try {
                    $playerVersion = PlayerHelper::getLastVersion($player);
                } catch (NoVersionException $e) { // It's a new player !
                    $playerVersion = (new PlayerVersion());
                    $player->addVersion($playerVersion);
                    $version->addPlayerVersion($playerVersion);
                    $this->setPlayerDefaultValues($playerVersion);
                }
                if (!$playerVersion->isMissingNextGame() && !($this->playerIsDisposable($playerVersion) && $excludeDisposable)) {
                    $value += $playerVersion->getValue();
                }
            }
        }
        // Sidelines
        $value += $version->getRerolls() * $this->getRerollCost($version->getTeam());
        $value += $version->getAssistants() * $this->getAssistantsCost($version->getTeam());
        $value += $version->getCheerleaders() * $this->getCheerleadersCost($version->getTeam());
        $value += $version->getPopularity() * $this->getPopularityCost($version->getTeam());
        $value += ($version->getApothecary()) ? $this->getApothecaryCost($version->getTeam()) : 0;

        return $value;
    }

    abstract public function setPlayerDefaultValues(PlayerVersion $version): ?PlayerVersion;
    abstract public function playerIsDisposable(PlayerVersion $version):bool;

    /**
     * @return array
     */
    public function getInjuriesTable():array
    {
        return $this->getInjuries();
    }

    public function getMaxPlayersByType($rosterKey, $typeKey): int
    {
        /** @var Roster $roster */
        $roster = $this->getRosters()->get($rosterKey);
        if (!$type = $roster->getPlayerTypes()[$typeKey]) {
            throw new NotFoundRuleKeyExcepion($typeKey, 'toto');
        }
        return (int) $type['max'];
    }
}
