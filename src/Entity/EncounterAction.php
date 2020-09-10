<?php

namespace Obblm\Core\Entity;

use Obblm\Core\Repository\EncounterActionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EncounterActionRepository::class)
 */
class EncounterAction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Encounter::class, inversedBy="actions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $encounter;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="encounterActions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $actions = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $injuries = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $owned_skills = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $fire = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInjuries(): ?array
    {
        return $this->injuries;
    }

    public function setInjuries(?array $injuries): self
    {
        $this->injuries = $injuries;

        return $this;
    }

    public function getEncounter(): ?Encounter
    {
        return $this->encounter;
    }

    public function setEncounter(?Encounter $encounter): self
    {
        $this->encounter = $encounter;

        return $this;
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

    public function getOwnedSkills(): ?array
    {
        return $this->owned_skills;
    }

    public function setOwnedSkills(?array $owned_skills): self
    {
        $this->owned_skills = $owned_skills;

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
