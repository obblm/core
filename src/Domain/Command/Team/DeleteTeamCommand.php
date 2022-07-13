<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Command\Team;

use Obblm\Core\Domain\Command\CommandInterface;
use Obblm\Core\Domain\Model\Team;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class DeleteTeamCommand implements CommandInterface
{
    /**
     * @var int|Team
     * @Assert\NotBlank()
     */
    private $team;

    /**
     * DeleteTeamCommand constructor.
     *
     * @param int|Team $team
     */
    public function __construct($team)
    {
        $this->team = $team;
    }

    /** @return int|Team */
    public function getTeam()
    {
        return $this->team;
    }

    public static function fromObject(Team $team): DeleteTeamCommand
    {
        return new DeleteTeamCommand($team);
    }

    public static function fromArray(array $data): DeleteTeamCommand
    {
        return new DeleteTeamCommand($data['team']);
    }

    public static function fromRequest(Request $request): DeleteTeamCommand
    {
        return new DeleteTeamCommand($request->get('team'));
    }
}
