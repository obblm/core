<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Command\Team;

use Obblm\Core\Domain\Command\AbstractCommand;
use Obblm\Core\Domain\Command\CommandInterface;
use Obblm\Core\Domain\Model\Coach;
use Obblm\Core\Domain\Model\Rule;

final class CreateTeamCommand extends AbstractCommand implements CommandInterface
{
    private string $name;
    private string $roster;
    private $coach;
    private $rule;

    const CONSTRUCTOR_ARGUMENTS = ['name', 'roster', 'coach', 'rule'];

    public function __construct($name, $roster, $coach, $rule)
    {
        $this->name = $name;
        $this->roster = $roster;
        $this->coach = $coach;
        $this->rule = $rule;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRoster(): string
    {
        return $this->roster;
    }

    /** @return string|Coach */
    public function getCoach()
    {
        return $this->coach;
    }

    /** @return string|Rule */
    public function getRule()
    {
        return $this->rule;
    }
}
