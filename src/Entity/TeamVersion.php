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
    private $td_give = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $td_take = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $injury_give = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $injury_take = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $game_win = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $game_draw = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $game_loss = 0;

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
        return $this->td_give;
    }

    public function setTdGive(int $td_give): self
    {
        $this->td_give = $td_give;

        return $this;
    }

    public function getTdTake(): ?int
    {
        return $this->td_take;
    }

    public function setTdTake(int $td_take): self
    {
        $this->td_take = $td_take;

        return $this;
    }

    public function getInjuryGive(): ?int
    {
        return $this->injury_give;
    }

    public function setInjuryGive(int $injury_give): self
    {
        $this->injury_give = $injury_give;

        return $this;
    }

    public function getInjuryTake(): ?int
    {
        return $this->injury_take;
    }

    public function setInjuryTake(int $injury_take): self
    {
        $this->injury_take = $injury_take;

        return $this;
    }

    public function getGameWin(): ?int
    {
        return $this->game_win;
    }

    public function setGameWin(int $game_win): self
    {
        $this->game_win = $game_win;

        return $this;
    }

    public function getGameDraw(): ?int
    {
        return $this->game_draw;
    }

    public function setGameDraw(int $game_draw): self
    {
        $this->game_draw = $game_draw;

        return $this;
    }

    public function getGameLoss(): ?int
    {
        return $this->game_loss;
    }

    public function setGameLoss(int $game_loss): self
    {
        $this->game_loss = $game_loss;

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
