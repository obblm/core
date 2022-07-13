<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Command\League;

use Obblm\Core\Domain\Command\CommandInterface;
use Obblm\Core\Domain\Model\Coach;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateLeagueCommand implements CommandInterface
{
    /**
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @Assert\NotBlank()
     */
    private Coach $admin;

    /**
     * CreateCoachCommand constructor.
     */
    public function __construct(
        string $name,
        Coach $admin
    ) {
        $this->name = $name;
        $this->admin = $admin;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAdmin(): Coach
    {
        return $this->admin;
    }

    public static function fromArray($data): CreateLeagueCommand
    {
        return new CreateLeagueCommand(
            $data['name'],
            $data['admin']
        );
    }

    public static function fromRequest(Request $request): CreateLeagueCommand
    {
        return new CreateLeagueCommand(
            $request->get('name'),
            $request->get('admin')
        );
    }
}
