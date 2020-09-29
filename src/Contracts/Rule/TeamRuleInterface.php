<?php

namespace Obblm\Core\Contracts\Rule;

use Doctrine\Common\Collections\ArrayCollection;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;

/****************************
 * TEAM INFORMATION METHODS
 ***************************/
interface TeamRuleInterface
{
    public function getAvailableRosters():ArrayCollection;
    public function getInjuriesTable():array;
    public function calculateTeamValue(TeamVersion $version, bool $excludeDisposable = false):int;
    public function calculateTeamRate(TeamVersion $version):?int;
    public function getMaxTeamCost():int;
    public function getRerollCost(Team $team):int;
    public function getApothecaryCost(Team $team):int;
    public function getCheerleadersCost(Team $team):int;
    public function getAssistantsCost(Team $team):int;
    public function getPopularityCost(Team $team):int;
    public function couldHaveApothecary(Team $team):bool;
}
