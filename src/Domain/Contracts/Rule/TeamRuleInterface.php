<?php

namespace Obblm\Core\Domain\Contracts\Rule;

use Obblm\Core\Domain\Model\PlayerVersion;
use Obblm\Core\Domain\Model\Team;
use Obblm\Core\Domain\Model\TeamVersion;

interface TeamRuleInterface
{
    public function getMaxTeamCost(Team $team = null): int;

    public function getRerollCost(Team $team): int;

    public function getApothecaryCost(Team $team): int;

    public function getCheerleadersCost(Team $team): int;

    public function getAssistantsCost(Team $team): int;

    public function getPopularityCost(Team $team): int;

    public function couldHaveApothecary(Team $team): bool;

    public function calculateTeamRate(TeamVersion $version): ?int;

    public function calculateInducementsCost(array $inducements): int;

    public function updatePlayerVersionCost(PlayerVersion $playerVersion);

    public function calculateTeamValue(TeamVersion $version, bool $excludeDisposable = false): int;

    public function getMaxPlayersByType($rosterKey, $typeKey): int;

    public function applyTeamExtraCosts(TeamVersion $version, $creationPhase = false);
}
