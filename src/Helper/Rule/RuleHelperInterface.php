<?php

namespace Obblm\Core\Helper\Rule;

use Obblm\Core\Entity\PlayerVersion;
use Obblm\Core\Entity\Rule;
use Obblm\Core\Entity\Team;
use Obblm\Core\Entity\TeamVersion;

interface RuleHelperInterface {
    public function getKey():string;
    public function getActionsFormClass():string;
    public function getInjuriesFormClass():string;
    public function getTemplateKey():string;
    public function attachRule(Rule $rule):self;
    public function getAttachedRule():Rule;
    public function getInjuriesTable():array;
    public function getInjury($key):?object;
    public function getSppLevel(PlayerVersion $version):?string;
    public function setDefaultValues(PlayerVersion $version):?PlayerVersion;
    public function calculateTeamValue(TeamVersion $version, bool $excludeDisposable = false):?int;
    public function calculateTeamRate(TeamVersion $version):?int;
    public function playerIsDisposable(PlayerVersion $playerVersion):bool;
    public function getMaxTeamCost():int;
}
