<?php

namespace Obblm\Core\Entity;

use Obblm\Core\Repository\PlayerVersionRepository;
use Obblm\Core\Service\PlayerService;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerVersionRepository::class)
 * @ORM\Table(name="obblm_team_player_version")
 * @ORM\HasLifecycleCallbacks
 */
class PlayerVersion
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="versions", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity=TeamVersion::class, inversedBy="playerVersions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $teamVersion;

    /**
     * @ORM\Column(type="array")
     */
    private $characteristics = [];

    /**
     * @ORM\Column(type="array")
     */
    private $skills = [];

    /**
     * @ORM\Column(type="array")
     */
    private $injuries = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $actions = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $missing_next_game = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $dead = false;

    /**
     * @ORM\Column(type="integer")
     */
    private $spp = 0;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $spp_level;

    /**
     * @ORM\Column(type="integer")
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getCharacteristics(): ?array
    {
        return $this->characteristics;
    }

    public function setCharacteristics(array $characteristics): self
    {
        $this->characteristics = $characteristics;

        return $this;
    }

    public function getSkills(): ?array
    {
        return $this->skills;
    }

    public function setSkills(array $skills): self
    {
        $this->skills = $skills;

        return $this;
    }

    public function getInjuries(): ?array
    {
        return $this->injuries;
    }

    public function setInjuries(array $injuries): self
    {
        $this->injuries = $injuries;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getMissingNextGame(): ?bool
    {
        return $this->missing_next_game;
    }

    public function isMissingNextGame(): ?bool
    {
        return $this->getMissingNextGame();
    }

    public function setMissingNextGame(bool $missing_next_game): self
    {
        $this->missing_next_game = $missing_next_game;
        return $this;
    }

    public function getDead(): ?bool
    {
        return $this->dead;
    }

    public function isDead(): ?bool
    {
        return $this->getDead();
    }

    public function setDead(bool $dead): self
    {
        $this->dead = $dead;
        $this->getPlayer()->setDead($this->dead);
        return $this;
    }

    public function getSpp(): ?int
    {
        return $this->spp;
    }

    public function setSpp(int $spp): self
    {
        $this->spp = $spp;

        return $this;
    }

    public function getActions(): ?array
    {
        return $this->actions;
    }

    public function setActions(?array $actions): self
    {
        $this->actions = $actions;

        return $this;
    }

    public function getTeamVersion(): ?TeamVersion
    {
        return $this->teamVersion;
    }

    public function setTeamVersion(?TeamVersion $teamVersion): self
    {
        $this->teamVersion = $teamVersion;

        return $this;
    }

    /**
     * @ORM\PostLoad
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function loadDefaultDatas(): void
    {
        if($this->getPlayer()) {
            if(!$this->getPlayer()->getTeam()) {
                $this->getPlayer()->setTeam($this->getTeamVersion()->getTeam());
            }
            if(!$this->getCharacteristics()) {
                $this->setCharacteristics(PlayerService::getPlayerCharacteristics($this->getPlayer()));
            }
            if(!$this->getSkills()) {
                $this->setSkills(PlayerService::getPlayerSkills($this->getPlayer()));
            }
        }
    }

    public function getSppLevel(): ?string
    {
        return $this->spp_level;
    }

    public function setSppLevel(string $spp_level): self
    {
        $this->spp_level = $spp_level;

        return $this;
    }
}
