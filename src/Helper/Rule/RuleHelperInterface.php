<?php

namespace Obblm\Core\Helper\Rule;

use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;

interface RuleHelperInterface
{
    /****************
     * COMPLIER PASS
     ****************/
    public function getKey():string;
    public function attachRule(Rule $rule):self;
    public function getAttachedRule():Rule;
    /**********************
     * APPLICATION METHODS
     *********************/
    public function getActionsFormClass():string;
    public function getInjuriesFormClass():string;
    public function getTemplateKey():string;
    public function getAvailablePlayerKeyTypes(string $roster):array;
    public function getAvailablePlayerTypes(string $roster):array;

    public function getInjuriesTable():array;
    public function getInjury($key):?object;
    public function getSppLevel(PlayerVersion $version):?string;
    public function setPlayerDefaultValues(PlayerVersion $version):?PlayerVersion;
    public function calculateTeamValue(TeamVersion $version, bool $excludeDisposable = false):?int;
    public function calculateTeamRate(TeamVersion $version):?int;
    public function playerIsDisposable(PlayerVersion $playerVersion):bool;
    public function getMaxTeamCost():int;
    public function getRerollCost(Team $team):int;
    public function getApothecaryCost(Team $team):int;
    public function getCheerleadersCost(Team $team):int;
    public function getAssistantsCost(Team $team):int;
    public function getPopularityCost(Team $team):int;
    public function couldHaveApothecary(Team $team):bool;
}
