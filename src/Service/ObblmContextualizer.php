<?php

namespace Obblm\Core\Service;

use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\Rule\RuleHelperInterface;

class ObblmContextualizer {
    protected $rule;
    protected $team;

    public function __construct() {}

    public function getRule():?RuleHelperInterface {
        return $this->rule;
    }

    public function setRule(RuleHelperInterface $rule) {
        $this->rule = $rule;
    }

    public function getTeam():?Team {
        return $this->team;
    }

    public function setteam(Team $team) {
        $this->team = $team;
    }
}