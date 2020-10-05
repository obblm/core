<?php

namespace Obblm\Core\Entity;

use Obblm\Core\Entity\Traits\SidelinesTrait;
use Obblm\Core\Entity\Traits\TeamEvolutionTrait;
use Obblm\Core\Entity\Traits\TimeStampableTrait;
use Obblm\Core\Repository\TeamVersionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TeamVersionRepository::class)
 * @ORM\Table(name="obblm_team_version")
 * @ORM\HasLifecycleCallbacks
 */
class TeamVersion
{
    use TimeStampableTrait, SidelinesTrait, TeamEvolutionTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, fetch="EAGER", inversedBy="versions", cascade="persist")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team;

    /**
     * @ORM\OneToMany(targetEntity=PlayerVersion::class, mappedBy="teamVersion", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $playerVersions;

    /**
     * @ORM\Column(type="integer")
     */
    private $treasure = 0;

    public function __construct()
    {
        $this->playerVersions = new ArrayCollection();
        $this->apothecary = false;
        $this->rerolls = 0;
        $this->cheerleaders = 0;
        $this->assistants = 0;
        $this->popularity = 0;
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
    public function getNotDeadPlayerVersions(): Collection
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('dead', false))
        ;
        return $this->playerVersions->matching($criteria);
    }

    /**
     * @return Collection|PlayerVersion[]
     */
    public function getAvailablePlayerVersions(): Collection
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
