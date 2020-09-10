<?php

namespace Obblm\Core\Entity;

use Obblm\Core\Repository\EncounterRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EncounterRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Encounter
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $validated_at;

    /**
     * @ORM\ManyToOne(targetEntity=Coach::class)
     */
    private $validated_by;

    /**
     * @ORM\ManyToOne(targetEntity=Championship::class, inversedBy="encounters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $championship;

    /**
     * @ORM\ManyToOne(targetEntity=Journey::class, inversedBy="encounters")
     */
    private $journey;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="encounters_as_home")
     * @ORM\JoinColumn(nullable=false)
     */
    private $home_team;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="encounters_as_visitor")
     * @ORM\JoinColumn(nullable=false)
     */
    private $visitor_team;

    /**
     * @ORM\OneToMany(targetEntity=EncounterAction::class, mappedBy="encounter", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $actions;

    private $home_actions;
    private $visitor_actions;

    /**
     * @ORM\OneToMany(targetEntity=TeamVersion::class, mappedBy="encounter", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $teamVersions;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $home_score;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $visitor_score;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $home_injury;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $visitor_injury;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $home_pop_earn;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $visitor_pop_earn;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $home_money_earn;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $visitor_money_earn;

    public function __construct()
    {
        $this->teamVersions = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->home_actions = new ArrayCollection();
        $this->visitor_actions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getValidatedAt(): ?DateTimeInterface
    {
        return $this->validated_at;
    }

    public function setValidatedAt(?DateTimeInterface $validated_at): self
    {
        $this->validated_at = $validated_at;

        return $this;
    }

    public function getChampionship(): ?Championship
    {
        return $this->championship;
    }

    public function setChampionship(?Championship $championship): self
    {
        $this->championship = $championship;

        return $this;
    }

    public function getJourney(): ?Journey
    {
        return $this->journey;
    }

    public function setJourney(?Journey $journey): self
    {
        $this->journey = $journey;

        return $this;
    }

    public function getHomeTeam(): ?Team
    {
        return $this->home_team;
    }

    public function setHomeTeam(?Team $home_team): self
    {
        $this->home_team = $home_team;

        return $this;
    }

    public function getVisitorTeam(): ?Team
    {
        return $this->visitor_team;
    }

    public function setVisitorTeam(?Team $visitor_team): self
    {
        $this->visitor_team = $visitor_team;

        return $this;
    }

    /**
     * @return Collection|EncounterAction[]
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(EncounterAction $action): self
    {
        if (!$this->actions->contains($action)) {
            $this->actions[] = $action;
            $action->setEncounter($this);
        }
        if($action->getPlayer()->getTeam() === $this->getHomeTeam()) {
            if (!$this->home_actions->contains($action)) {
                $this->home_actions[] = $action;
            }
        }
        elseif($action->getPlayer()->getTeam() === $this->getVisitorTeam()) {
            if (!$this->visitor_actions->contains($action)) {
                $this->visitor_actions[] = $action;
            }
        }

        return $this;
    }

    public function removeAction(EncounterAction $action): self
    {
        if ($this->actions->contains($action)) {
            $this->actions->removeElement($action);
            // set the owning side to null (unless already changed)
            if ($action->getEncounter() === $this) {
                $action->setEncounter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EncounterAction[]
     */
    public function getHomeActions(): ?Collection
    {
        return $this->home_actions;
    }
    public function getVisitorActions(): ?Collection
    {
        return $this->visitor_actions;
    }

    public function addHomeAction(EncounterAction $action): self
    {
        return $this->addAction($action);
    }

    public function addVisitorAction(EncounterAction $action): self
    {
        return $this->addAction($action);
    }

    public function removeHomeAction(EncounterAction $action): self
    {
        if ($this->home_actions->contains($action)) {
            $this->home_actions->removeElement($action);
            $this->actions->removeElement($action);
            // set the owning side to null (unless already changed)
            if ($action->getEncounter() === $this) {
                $action->setEncounter(null);
            }
        }

        return $this;
    }

    public function removeVisitorAction(EncounterAction $action): self
    {
        if ($this->visitor_actions->contains($action)) {
            $this->visitor_actions->removeElement($action);
            $this->actions->removeElement($action);
            // set the owning side to null (unless already changed)
            if ($action->getEncounter() === $this) {
                $action->setEncounter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TeamVersion[]
     */
    public function getTeamVersions(): Collection
    {
        return $this->teamVersions;
    }

    public function addTeamVersion(TeamVersion $teamVersion): self
    {
        if (!$this->teamVersions->contains($teamVersion)) {
            $this->teamVersions[] = $teamVersion;
            $teamVersion->setEncounter($this);
        }

        return $this;
    }

    public function removeTeamVersion(TeamVersion $teamVersion): self
    {
        if ($this->teamVersions->contains($teamVersion)) {
            $this->teamVersions->removeElement($teamVersion);
            // set the owning side to null (unless already changed)
            if ($teamVersion->getEncounter() === $this) {
                $teamVersion->setEncounter(null);
            }
        }

        return $this;
    }

    /**
     * @ORM\PostLoad
     */
    public function loadHomeVisitorActions(): void
    {
        $this->home_actions = new ArrayCollection();
        $this->visitor_actions = new ArrayCollection();
        foreach($this->getActions() as $action) {
            if($action->getPlayer()->getTeam() === $this->getHomeTeam()) {
                $this->home_actions[] = $action;
            }
            elseif($action->getPlayer()->getTeam() === $this->getVisitorTeam()) {
                $this->visitor_actions[] = $action;
            }
        }
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        foreach($this->getHomeActions() as $action) {
            $this->addAction($action);
        }
        foreach($this->getVisitorActions() as $action) {
            $this->addAction($action);
        }
        $this->setUpdatedAt(new DateTime('now'));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new DateTime('now'));
        }
    }

    public function getValidatedBy(): ?Coach
    {
        return $this->validated_by;
    }

    public function setValidatedBy(?Coach $validated_by): self
    {
        $this->validated_by = $validated_by;

        return $this;
    }

    public function getHomeScore(): ?int
    {
        return $this->home_score;
    }

    public function setHomeScore(?int $home_score): self
    {
        $this->home_score = $home_score;

        return $this;
    }

    public function getVisitorScore(): ?int
    {
        return $this->visitor_score;
    }

    public function setVisitorScore(?int $visitor_score): self
    {
        $this->visitor_score = $visitor_score;

        return $this;
    }

    public function getHomePopEarn(): ?int
    {
        return $this->home_pop_earn;
    }

    public function setHomePopEarn(?int $home_pop_earn): self
    {
        $this->home_pop_earn = $home_pop_earn;

        return $this;
    }

    public function getVisitorPopEarn(): ?int
    {
        return $this->visitor_pop_earn;
    }

    public function setVisitorPopEarn(?int $visitor_pop_earn): self
    {
        $this->visitor_pop_earn = $visitor_pop_earn;

        return $this;
    }

    public function getHomeMoneyEarn(): ?int
    {
        return $this->home_money_earn;
    }

    public function setHomeMoneyEarn(?int $home_money_earn): self
    {
        $this->home_money_earn = $home_money_earn;

        return $this;
    }

    public function getVisitorMoneyEarn(): ?int
    {
        return $this->visitor_money_earn;
    }

    public function setVisitorMoneyEarn(?int $visitor_money_earn): self
    {
        $this->visitor_money_earn = $visitor_money_earn;

        return $this;
    }

    public function getHomeInjury(): ?int
    {
        return $this->home_injury;
    }

    public function setHomeInjury(?int $home_injury): self
    {
        $this->home_injury = $home_injury;

        return $this;
    }

    public function getVisitorInjury(): ?int
    {
        return $this->visitor_injury;
    }

    public function setVisitorInjury(?int $visitor_injury): self
    {
        $this->visitor_injury = $visitor_injury;

        return $this;
    }
}
