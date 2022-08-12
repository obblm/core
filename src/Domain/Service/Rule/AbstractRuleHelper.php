<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Service\Rule;

use Obblm\Core\Domain\Contracts\RuleHelperInterface;
use Obblm\Core\Domain\Model\Rule;
use Obblm\Core\Domain\Service\Rule\Config\RuleConfigBuilder;
use Obblm\Core\Domain\Service\Rule\Traits\TeamRuleTrait;

abstract class AbstractRuleHelper extends RuleConfigBuilder implements RuleHelperInterface
{
    use TeamRuleTrait;
    protected string $ruleId = '';
    protected string $key = '';
    protected string $path = '';
    protected string $type = '';
    protected array $rule = [];

    public function getKey(): string
    {
        return $this->key;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getWeatherChoices(): array
    {
        // TODO: Implement getWeatherChoices() method.
    }

    public function attachRule(Rule $rule): RuleHelperInterface
    {
        $this->ruleId = $rule->getId();
        $this->key = $rule->getRuleKey();
        $this->path = $rule->getRuleDirectory();
        $this->type = $rule->getTemplate();
        $this->rule = $rule->getRule();
        $this->build($this->key, $rule->getRule());

        return $this;
    }
}
