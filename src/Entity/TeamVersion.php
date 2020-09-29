<?php

namespace Obblm\Core\Entity;

use Obblm\Core\Repository\TeamVersionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TeamVersionRepository::class)
 * @ORM\Table(name="obblm_team_version")
 * @ORM\HasLifecycleCallbacks()
 */
class TeamVersion
{
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
    private $rerolls;

    /**
     * @ORM\Column(type="integer")
     */
    private $cheerleaders;

    /**
     * @ORM\Column(type="integer")
     */
    private $assistants;

    /**
     * @ORM\Column(type="integer")
     */
    private $popularity;

    /**
     * @ORM\Column(type="boolean")
     */
    private $apothecary;

    /**
     * @ORM\Column(type="integer")
     */
    private $points = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $tdGive = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $tdTake = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $injuryGive = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $injuryTake = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $gameWin = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $gameDraw = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $gameLoss = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $treasure = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $tr = 0;

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

    public function getRerolls(): ?int
    {
        return $this->rerolls;
    }

    public function setRerolls(int $rerolls): self
    {
        $this->rerolls = $rerolls;

        return $this;
    }

    public function getCheerleaders(): ?int
    {
        return $this->cheerleaders;
    }

    public function setCheerleaders(int $cheerleaders): self
    {
        $this->cheerleaders = $cheerleaders;

        return $this;
    }

    public function getAssistants(): ?int
    {
        return $this->assistants;
    }

    public function setAssistants(int $assistants): self
    {
        $this->assistants = $assistants;

        return $this;
    }

    public function getPopularity(): ?int
    {
        return $this->popularity;
    }

    public function setPopularity(int $popularity): self
    {
        $this->popularity = $popularity;

        return $this;
    }

    public function getApothecary(): ?bool
    {
        return $this->apothecary;
    }

    public function setApothecary(bool $apothecary): self
    {
        $this->apothecary = $apothecary;

        return $this;
    }

    public function getTdGive(): ?int
    {
        return $this->tdGive;
    }

    public function setTdGive(int $tdGive): self
    {
        $this->tdGive = $tdGive;

        return $this;
    }

    public function getTdTake(): ?int
    {
        return $this->tdTake;
    }

    public function setTdTake(int $tdTake): self
    {
        $this->tdTake = $tdTake;

        return $this;
    }

    public function getInjuryGive(): ?int
    {
        return $this->injuryGive;
    }

    public function setInjuryGive(int $injuryGive): self
    {
        $this->injuryGive = $injuryGive;

        return $this;
    }

    public function getInjuryTake(): ?int
    {
        return $this->injuryTake;
    }

    public function setInjuryTake(int $injuryTake): self
    {
        $this->injuryTake = $injuryTake;

        return $this;
    }

    public function getGameWin(): ?int
    {
        return $this->gameWin;
    }

    public function setGameWin(int $gameWin): self
    {
        $this->gameWin = $gameWin;

        return $this;
    }

    public function getGameDraw(): ?int
    {
        return $this->gameDraw;
    }

    public function setGameDraw(int $gameDraw): self
    {
        $this->gameDraw = $gameDraw;

        return $this;
    }

    public function getGameLoss(): ?int
    {
        return $this->gameLoss;
    }

    public function setGameLoss(int $gameLoss): self
    {
        $this->gameLoss = $gameLoss;

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

    public function getTr(): ?int
    {
        return $this->tr;
    }

    public function setTr(int $tr): self
    {
        $this->tr = $tr;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }
}
