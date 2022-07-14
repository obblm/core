<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Command\Team;

use Obblm\Core\Domain\Command\CommandInterface;
use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Model\Rule;
use Obblm\Core\Domain\Model\Team;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateTeamCommand implements CommandInterface
{
    protected $name;
    protected $coach;
    protected $rule;

    public function __construct($name, $coach, $rule)
    {
        $this->name = $name;
        $this->coach = $coach;
        $this->rule = $rule;
    }

    public function getName()
    {
        return $this->name;
    }

    /** @return string|Uuid|Coach */
    public function getCoach()
    {
        return $this->coach;
    }

    /** @return string|Uuid|Rule */
    public function getRule()
    {
        return $this->rule;
    }

    public static function fromArray(array $data): CreateTeamCommand
    {
        return new CreateTeamCommand($data['name'], $data['coach'], $data['rule']);
    }

    public static function fromRequest(Request $request): CreateTeamCommand
    {
        return new CreateTeamCommand($request->get('name'), $request->get('coach'), $request->get('rule'));
    }
}
