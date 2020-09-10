<?php

namespace Obblm\Core\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Obblm\Core\Repository\LeagueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=LeagueRepository::class)
 */
class League
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
     * @ORM\OneToMany(targetEntity=Championship::class, mappedBy="league", orphanRemoval=true)
     */
    private $championships;

    /**
     * @ORM\ManyToOne(targetEntity=Coach::class, inversedBy="admin_leagues")
     */
    private $owner;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_private = false;

    public function __construct()
    {
        $this->championships = new ArrayCollection();
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

    /**
     * @return Collection|Championship[]
     */
    public function getChampionships(): Collection
    {
        return $this->championships;
    }

    public function addChampionship(Championship $championship): self
    {
        if (!$this->championships->contains($championship)) {
            $this->championships[] = $championship;
            $championship->setLeague($this);
        }

        return $this;
    }

    public function removeChampionship(Championship $championship): self
    {
        if ($this->championships->contains($championship)) {
            $this->championships->removeElement($championship);
            // set the owning side to null (unless already changed)
            if ($championship->getLeague() === $this) {
                $championship->setLeague(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?Coach
    {
        return $this->owner;
    }

    public function setOwner(?Coach $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function isPrivate(): ?bool
    {
        return $this->is_private;
    }

    public function getIsPrivate(): ?bool
    {
        return $this->is_private;
    }

    public function setIsPrivate(bool $is_private): self
    {
        $this->is_private = $is_private;

        return $this;
    }
}
