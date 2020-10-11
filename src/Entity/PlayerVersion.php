<?php

namespace Obblm\Core\Entity;

use Obblm\Core\Entity\Traits\DeadAndFireTrait;
use Obblm\Core\Helper\PlayerHelper;
use Obblm\Core\Repository\PlayerVersionRepository;
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

    use DeadAndFireTrait;

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
     * @ORM\Column(type="array", nullable=true)
     */
    private $additionalSkills = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $injuries = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $actions = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $missingNextGame = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hiredStarPlayer = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mercenary = false;

    /**
     * @ORM\Column(type="integer")
     */
    private $spp = 0;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sppLevel;

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

    public function getAdditionalSkills(): ?array
    {
        return $this->additionalSkills;
    }

    public function setAdditionalSkills(array $additionalSkills): self
    {
        $this->additionalSkills = $additionalSkills;

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
        return $this->missingNextGame;
    }

    public function isMissingNextGame(): ?bool
    {
        return $this->getMissingNextGame();
    }

    public function setMissingNextGame(bool $missingNextGame): self
    {
        $this->missingNextGame = $missingNextGame;
        return $this;
    }

    public function setDead(bool $dead): self
    {
        $this->dead = $dead;
        $this->getPlayer()->setDead($this->dead);
        return $this;
    }

    public function setFire(bool $fire): self
    {
        $this->fire = $fire;
        $this->getPlayer()->setFire($this->fire);
        return $this;
    }

    public function getHiredStarPlayer(): ?bool
    {
        return $this->hiredStarPlayer;
    }

    public function isHiredStarPlayer(): ?bool
    {
        return $this->getHiredStarPlayer();
    }

    public function setHiredStarPlayer(bool $hiredStarPlayer): self
    {
        $this->hiredStarPlayer = $hiredStarPlayer;
        return $this;
    }

    public function getMercenary(): ?bool
    {
        return $this->mercenary;
    }

    public function isMercenary(): ?bool
    {
        return $this->getMercenary();
    }

    public function setMercenary(bool $mercenary): self
    {
        $this->mercenary = $mercenary;
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
        if ($this->getPlayer()) {
            if (!$this->getPlayer()->getTeam()) {
                $this->getPlayer()->setTeam($this->getTeamVersion()->getTeam());
            }
            if (!$this->getCharacteristics()) {
                $this->setCharacteristics(PlayerHelper::getPlayerCharacteristics($this->getPlayer()));
            }
            if (!$this->getSkills()) {
                $this->setSkills(PlayerHelper::getPlayerSkills($this->getPlayer()));
            }
        }
    }

    public function getSppLevel(): ?string
    {
        return $this->sppLevel;
    }

    public function setSppLevel(string $sppLevel): self
    {
        $this->sppLevel = $sppLevel;

        return $this;
    }

    public function __toString()
    {
        return $this->getPlayer()->getName() . "#" . $this->getId();
    }
}
