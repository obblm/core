<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Model\Doctrine\Traits;

use Doctrine\ORM\Mapping as ORM;

trait DeadAndFireTrait
{
    /**
     * @ORM\Column(type="boolean")
     */
    private $dead = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fired = false;

    public function getDead(): ?bool
    {
        return $this->dead;
    }

    public function isDead(): ?bool
    {
        return $this->dead;
    }

    public function setDead(bool $dead): self
    {
        $this->dead = $dead;

        return $this;
    }

    public function getFire(): ?bool
    {
        return $this->fired;
    }

    public function isFired(): ?bool
    {
        return $this->fired;
    }

    public function setFired(bool $fired): self
    {
        $this->fired = $fired;

        return $this;
    }
}
