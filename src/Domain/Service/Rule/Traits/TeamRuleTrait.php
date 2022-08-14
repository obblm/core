<?php

namespace Obblm\Core\Domain\Service\Rule\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Obblm\Core\Domain\Contracts\Rule\PositionInterface;
use Obblm\Core\Domain\Contracts\Rule\RosterInterface;
use Obblm\Core\Domain\Model\PlayerVersion;
use Obblm\Core\Domain\Model\Proxy\Roster\Roster;
use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Model\TeamVersion;

/****************************
 * TEAM INFORMATION METHODS
 ***************************/
trait TeamRuleTrait
{
    abstract public function getRoster(Team $team): RosterInterface;

    public function getMaxTeamCost(Team $team = null): int
    {
        if ($team && $team->getCreationOption('max_team_cost')) {
            return $team->getCreationOption('max_team_cost');
        }

        return ($this->rule['max_team_cost']) ? $this->rule['max_team_cost'] : Value::LIMIT;
    }

    public function getRerollCost(Team $team): int
    {
        /** @var Roster $roster */
        $roster = $this->getRosters()->get($team->getRoster());

        return (int) $roster->getRerollCost();
    }

    public function getApothecaryCost(Team $team): int
    {
        return (int) $this->rule['sidelines_cost']['apothecary'];
    }

    public function getCheerleadersCost(Team $team): int
    {
        return (int) $this->rule['sidelines_cost']['cheerleaders'];
    }

    public function getAssistantsCost(Team $team): int
    {
        return (int) $this->rule['sidelines_cost']['assistants'];
    }

    public function getPopularityCost(Team $team): int
    {
        return (int) $this->rule['sidelines_cost']['popularity'];
    }

    /**
     * @throws \Exception
     */
    public function couldHaveApothecary(Team $team): bool
    {
        /** @var Roster $roster */
        $roster = $this->getRosters()->get($team->getRoster());

        return (bool) $roster->canHaveApothecary();
    }

    public function calculateTeamRate(TeamVersion $version): ?int
    {
        return $this->calculateTeamValue($version) / 10000;
    }

    public function calculateInducementsCost(array $inducements): int
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
        if ($playerVersion->getPlayer()->getTeam()->getCreationOption('skills_allowed') && AdditionalSkills::NOT_FREE == $playerVersion->getPlayer()->getTeam()->getCreationOption('skills_allowed.choice')) {
            if (!$playerVersion->isHiredStarPlayer() && !$playerVersion->getPlayer()->isStarPlayer()) {
                $extra = $this->getPlayerVersionExtraCosts($playerVersion);
                $playerVersion->setValue($position->getCost() + $extra);
            }
        }
    }

    public function calculateTeamValue(TeamVersion $version, bool $excludeDisposable = false): int
    {
        if (!$version->getTeam()) {
            throw new \InvalidArgumentException();
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

    //abstract public function setPlayerDefaultValues(PlayerVersion $version, PositionInterface $position): ?PlayerVersion;
    //abstract public function getPlayerPosition(PlayerVersion $version): PositionInterface;
    //abstract public function playerIsDisposable(PlayerVersion $version): bool;

    /** @return RosterInterface[]|ArrayCollection */
    abstract public function getRosters(): ArrayCollection;

    public function getInjuriesTable(): array
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
            AdditionalSkills::NOT_FREE == $version->getTeam()->getCreationOption('skills_allowed.choice')) {
            foreach ($version->getNotDeadPlayerVersions() as $playerVersion) {
                if (!$playerVersion->isHiredStarPlayer() && !$playerVersion->getPlayer()->isStarPlayer()) {
                    $extra = $this->getPlayerVersionExtraCosts($playerVersion);
                    $position = $this->getPlayerPosition($playerVersion->getPlayer());
                    $playerVersion->setValue($position->getCost() + $extra);
                }
            }
        }
    }
}
