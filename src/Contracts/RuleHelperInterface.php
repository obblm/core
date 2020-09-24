<?php

namespace Obblm\Core\Contracts;

use Obblm\Core\Contracts\Rule\ApplicativeRuleInterface;
use Obblm\Core\Contracts\Rule\InducementRuleInterface;
use Obblm\Core\Contracts\Rule\PlayerRuleInterface;
use Obblm\Core\Contracts\Rule\TeamRuleInterface;
use Obblm\Core\Entity\Rule;

interface RuleHelperInterface extends ApplicativeRuleInterface, TeamRuleInterface, PlayerRuleInterface, InducementRuleInterface
{
    /****************
     * COMPLIER PASS
     ****************/
    public function getKey():string;
    public function attachRule(Rule $rule):self;

    /**********
     * CACHING
     *********/
    public function getAttachedRule():?Rule;
    public function setAttachedRule(Rule $rule):self;

    /***************
     * MISC METHODS
     **************/
    public function getWeatherChoices():array;
}
