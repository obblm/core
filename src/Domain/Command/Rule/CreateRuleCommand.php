<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Command\Rule;

use Obblm\Core\Domain\Command\CommandInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateRuleCommand implements CommandInterface
{
    /**
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @Assert\NotBlank()
     */
    private string $ruleKey;

    /**
     * CreateCoachCommand constructor.
     */
    public function __construct(
        string $name,
        string $ruleKey
    ) {
        $this->name = $name;
        $this->ruleKey = $ruleKey;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRuleKey(): string
    {
        return $this->ruleKey;
    }

    public static function fromArray($data): CreateRuleCommand
    {
        return new CreateRuleCommand(
            $data['name'],
            $data['rule_key']
        );
    }

    public static function fromRequest(Request $request): CreateRuleCommand
    {
        return new CreateRuleCommand(
            $request->get('name'),
            $request->get('rule_key')
        );
    }
}
