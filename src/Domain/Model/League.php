<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model;

use Obblm\Core\Domain\Model\Traits\LogoTrait;
use Obblm\Core\Domain\Model\Traits\NameTrait;
use Obblm\Core\Domain\Model\Traits\TimeStampableTrait;
use Symfony\Component\Uid\Uuid;

class League
{
    use NameTrait;
    use LogoTrait;
    use TimeStampableTrait;

    private Uuid $id;
    private Coach $admin;
    private bool $canPlayersCreateTeams = false;

    public function getId(): ?Uuid
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
