<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Domain\Model\Traits\SidelinesTrait;
use Obblm\Core\Domain\Model\Traits\TeamEvolutionTrait;
use Obblm\Core\Domain\Model\Traits\TimeStampableTrait;

class TeamVersion
{
    use TimeStampableTrait;
    use SidelinesTrait;
    use TeamEvolutionTrait;

    private string $id;

    private Team $team;

    private Collection $playerVersions;

    private int $treasure = 0;

    public function __construct()
    {
        $this->playerVersions = new ArrayCollection();
        $this->apothecary = false;
        $this->rerolls = 0;
        $this->cheerleaders = 0;
        $this->assistants = 0;
        $this->popularity = 0;
    }

    public function getId(): ?string
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

    /**
     * @return Collection|PlayerVersion[]
     */
    public function getPlayerVersions(): Collection
    {
        return $this->playerVersions;
    }

    /**
     * @return Collection|PlayerVersion[]
     */
    public function getNotDeadPlayerVersions()
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('dead', false))
        ;

        return $this->playerVersions->matching($criteria);
    }

    /**
     * @return Collection|PlayerVersion[]
     */
    public function getAvailablePlayerVersions()
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('dead', false))
        ;

        return $this->playerVersions->matching($criteria);
    }

    public function addPlayerVersion(PlayerVersion $playerVersion): self
    {
        if (!$this->playerVersions->contains($playerVersion)) {
            $this->playerVersions[] = $playerVersion;
            $playerVersion->setTeamVersion($this);
        }

        return $this;
    }

    public function removePlayerVersion(PlayerVersion $playerVersion): self
    {
        if ($this->playerVersions->contains($playerVersion)) {
            $this->playerVersions->removeElement($playerVersion);
            // set the owning side to null (unless already changed)
            if ($playerVersion->getTeamVersion() === $this) {
                $playerVersion->setTeamVersion(null);
            }
        }

        return $this;
    }

    public function getTreasure(): ?int
    {
        return $this->treasure;
    }

    public function setTreasure(int $treasure): self
    {
        $this->treasure = $treasure;

        return $this;
    }

    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
        $this->getTeam()->setUpdatedAt(new \DateTime());
    }
}
