<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Command\League;

use Obblm\Core\Domain\Command\AbstractCommand;
use Obblm\Core\Domain\Command\CommandInterface;
use Obblm\Core\Domain\Model\Coach;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateLeagueCommand extends AbstractCommand implements CommandInterface
{
    public const CONSTRUCTOR_ARGUMENTS = ['name', 'admin'];

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
}
