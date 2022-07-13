<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Command\League;

use Obblm\Core\Domain\Command\CommandInterface;
use Obblm\Core\Domain\Model\Coach;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class EditLeagueCommand implements CommandInterface
{
    /**
     * @Assert\NotBlank()
     */
    private string $id;

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
        string $id,
        string $name,
        Coach $admin
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->admin = $admin;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAdmin(): Coach
    {
        return $this->admin;
    }

    public static function fromArray(array $data, string $relatedObjectId): EditLeagueCommand
    {
        return new EditLeagueCommand(
            $relatedObjectId,
            $data['name'],
            $data['admin']
        );
    }

    public static function fromRequest(Request $request): EditLeagueCommand
    {
        return new EditLeagueCommand(
            $request->get('id'),
            $request->get('name'),
            $request->get('admin')
        );
    }
}
