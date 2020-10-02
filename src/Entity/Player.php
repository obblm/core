<?php

namespace Obblm\Core\Entity;

use Obblm\Core\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 * @ORM\Table(name="obblm_team_player")
 */
class Player
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="players")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=PlayerVersion::class, mappedBy="player", orphanRemoval=true, cascade={"remove"})
     * @ORM\OrderBy({"id"="DESC"})
     */
    private $versions;

    /**
     * @ORM\Column(type="boolean")
     */
    private $dead = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fire = false;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|PlayerVersion[]
     */
    public function getVersions(): Collection
    {
        return $this->versions;
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

    public function getDead(): ?bool
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
        return $this->fire;
    }

    public function setFire(bool $fire): self
    {
        $this->fire = $fire;

        return $this;
    }
}
