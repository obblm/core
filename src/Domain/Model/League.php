<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model;

use Obblm\Core\Domain\Model\Traits\LogoTrait;
use Obblm\Core\Domain\Model\Traits\NameTrait;
use Obblm\Core\Domain\Model\Traits\TimeStampableTrait;

class League
{
    use NameTrait;
    use LogoTrait;
    use TimeStampableTrait;

    private $id;
    private Coach $admin;
    private bool $canPlayersCreateTeams = false;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAdmin(): Coach
    {
        return $this->admin;
    }

    public function setAdmin(Coach $coach): League
    {
        $this->admin = $coach;

        return $this;
    }

    public function isCanPlayersCreateTeams(): bool
    {
        return $this->canPlayersCreateTeams;
    }

    public function setCanPlayersCreateTeams(bool $canPlayersCreateTeams): League
    {
        $this->canPlayersCreateTeams = $canPlayersCreateTeams;

        return $this;
    }
}
