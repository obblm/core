<?php

namespace Obblm\Core\Helper\Rule\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Obblm\Core\Contracts\PositionInterface;
use Obblm\Core\Contracts\RosterInterface;
use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;
use Obblm\Core\Exception\InvalidArgumentException;
use Obblm\Core\Helper\PlayerHelper;
use Obblm\Core\Helper\Rule\Roster\Roster;
use Obblm\Core\Validator\Constraints\Team\AdditionalSkills;
use Obblm\Core\Validator\Constraints\Team\Value;

/****************************
 * TEAM INFORMATION METHODS
 ***************************/
trait AbstractTeamRuleTrait
{
    abstract public function getRoster(Team $team):RosterInterface;

    /**
     * @return int
     */
    public function getMaxTeamCost(Team $team = null):int
    {
        if ($team && $team->getCreationOption('max_team_cost')) {
            return $team->getCreationOption('max_team_cost');
        }

        return ($this->rule['max_team_cost']) ? $this->rule['max_team_cost'] : Value::LIMIT;
    }

    /**
     * @param Team $team
     * @return int
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

    public function calculateInducementsCost(array $inducements):int
    {
        $cost = 0;
        foreach ($inducements as $key) {
            $cost += $this->getInducement($key)->getValue();
        }
        return $cost;
    }

    public function updatePlayerVersionCost(PlayerVersion $playerVersion)
    {
        $position = $this->getPlayerPosition($playerVersion->getPlayer());
        if ($playerVersion->getPlayer()->getTeam()->getCreationOption('skills_allowed') && $playerVersion->getPlayer()->getTeam()->getCreationOption('skills_allowed.choice') == AdditionalSkills::NOT_FREE) {
            if (!$playerVersion->isHiredStarPlayer() && !$playerVersion->getPlayer()->isStarPlayer()) {
                $extra = $this->getPlayerVersionExtraCosts($playerVersion);
                $playerVersion->setValue($position->getCost() + $extra);
            }
        }
    }

    public function calculateTeamValue(TeamVersion $version, bool $excludeDisposable = false):int
    {
        if (!$version->getTeam()) {
            throw new InvalidArgumentException();
        }
        $value = 0;
        // Players
        $roster = $this->getRoster($version->getTeam());
        foreach ($version->getNotDeadPlayerVersions() as $playerVersion) {
            $player = $playerVersion->getPlayer();
            if ($player->getPosition()) {
                $playerVersion = PlayerHelper::getLastVersion($player);
                //$this->updatePlayerVersionCost($playerVersion);
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

    abstract public function setPlayerDefaultValues(PlayerVersion $version, PositionInterface $position): ?PlayerVersion;
    abstract public function playerIsDisposable(PlayerVersion $version):bool;
    /** @return RosterInterface[]|ArrayCollection */
    abstract public function getRosters():ArrayCollection;

    /**
     * @return array
     */
    public function getInjuriesTable():array
    {
        return (array) $this->getInjuries();
    }

    public function getMaxPlayersByType($rosterKey, $typeKey): int
    {
        /** @var PositionInterface $position */
        $position = $this->getRosters()->get($rosterKey)->getPosition($typeKey);
        return (int) $position->getMax();
    }

    public function applyTeamExtraCosts(TeamVersion $version, $creationPhase = false)
    {
        if ($creationPhase &&
            $version->getTeam()->getCreationOption('skills_allowed') &&
            $version->getTeam()->getCreationOption('skills_allowed.choice') == AdditionalSkills::NOT_FREE) {
            $this->applyPlayersExtraCosts($version);
        }
    }

    private function applyPlayersExtraCosts(TeamVersion $version)
    {
        foreach ($version->getNotDeadPlayerVersions() as $playerVersion) {
            if (!$playerVersion->isHiredStarPlayer() && !$playerVersion->getPlayer()->isStarPlayer()) {
                $extra = $this->getPlayerVersionExtraCosts($playerVersion);
                $position = $this->getPlayerPosition($playerVersion->getPlayer());
                $playerVersion->setValue($position->getCost() + $extra);
            }
        }
    }
}
