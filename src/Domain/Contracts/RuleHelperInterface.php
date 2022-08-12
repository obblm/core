<?php

namespace Obblm\Core\Domain\Contracts;

use Obblm\Core\Domain\Contracts\Rule\PlayerRuleInterface;
use Obblm\Core\Domain\Contracts\Rule\RuleBuilderInterface;
use Obblm\Core\Domain\Contracts\Rule\TeamRuleInterface;
use Obblm\Core\Domain\Model\Rule;

interface RuleHelperInterface extends RuleBuilderInterface, TeamRuleInterface, PlayerRuleInterface
{
    public function getKey(): string;

    public function getType(): string;

    public function attachRule(Rule $rule): self;
}
