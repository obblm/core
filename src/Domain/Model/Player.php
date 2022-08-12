<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Obblm\Core\Domain\Model\Traits\DeadAndFireTrait;
use Obblm\Core\Domain\Model\Traits\NameTrait;

class Player
{
    use NameTrait;
    use DeadAndFireTrait;

    private $id;

    private ?Team $team = null;

    private ?int $number = null;

    private ?string $position = null;

    private bool $starPlayer = false;

    private Collection $versions;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getStarPlayer(): ?bool
    {
        return $this->starPlayer;
    }

    public function isStarPlayer(): ?bool
    {
        return $this->starPlayer;
    }

    public function setStarPlayer(bool $starPlayer): self
    {
        $this->starPlayer = $starPlayer;

        return $this;
    }

    /**
     * @return Collection|PlayerVersion[]
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function getLastVersion(): ?PlayerVersion
    {
        return $this->versions->last();
    }

    public function addVersion(PlayerVersion $version): self
    {
        if (!$this->versions->contains($version)) {
            $this->versions[] = $version;
            $version->setPlayer($this);
        }

        return $this;
    }

    public function removeVersion(PlayerVersion $version): self
    {
        if ($this->versions->contains($version)) {
            $this->versions->removeElement($version);
            // set the owning side to null (unless already changed)
            if ($version->getPlayer() === $this) {
                $version->setPlayer(null);
            }
        }

        return $this;
    }
}
