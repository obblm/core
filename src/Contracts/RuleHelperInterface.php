<?php

namespace Obblm\Core\Contracts;

use Obblm\Core\Contracts\Rule\RuleApplicativeInterface;
use Obblm\Core\Contracts\Rule\RuleInducementInterface;
use Obblm\Core\Contracts\Rule\RulePlayerInterface;
use Obblm\Core\Contracts\Rule\RuleBuilderInterface;
use Obblm\Core\Contracts\Rule\RuleTeamInterface;
use Obblm\Core\Entity\Rule;

interface RuleHelperInterface extends RuleApplicativeInterface, RuleBuilderInterface, RuleTeamInterface, RulePlayerInterface, RuleInducementInterface
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
