<?php

namespace Obblm\Core\Entity;

use Obblm\Core\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 * @ORM\Table(name="obblm_team")
 */
class Team
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
     * @ORM\ManyToOne(targetEntity=Coach::class, inversedBy="teams")
     * @ORM\JoinColumn(nullable=false)
     */
    private $coach;

    /**
     * @ORM\ManyToOne(targetEntity=Rule::class, inversedBy="teams")
     */
    private $rule;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $anthem;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $fluff;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $roster;

    /**
     * @ORM\OneToMany(targetEntity=Player::class, fetch="EAGER", mappedBy="team", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\OrderBy({"number"="ASC"})
     */
    private $players;

    /**
     * @ORM\Column(type="boolean")
     */
    private $ready = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $locked_by_managment = false;

    /**
     * @ORM\OneToMany(targetEntity=TeamVersion::class, fetch="EAGER", mappedBy="team", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\OrderBy({"id"="DESC"})
     */
    private $versions;

    public function __construct()
    {
        $this->players = new ArrayCollection();
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

    public function getCoach(): ?Coach
    {
        return $this->coach;
    }

    public function setCoach(?Coach $coach): self
    {
        $this->coach = $coach;

        return $this;
    }

    public function getRule(): ?Rule
    {
        return $this->rule;
    }

    public function setRule(?Rule $rule): self
    {
        $this->rule = $rule;

        return $this;
    }

    public function getAnthem(): ?string
    {
        return $this->anthem;
    }

    public function setAnthem(?string $anthem): self
    {
        $this->anthem = $anthem;

        return $this;
    }

    public function getFluff(): ?string
    {
        return $this->fluff;
    }

    public function setFluff(?string $fluff): self
    {
        $this->fluff = $fluff;

        return $this;
    }

    public function getRoster(): ?string
    {
        return $this->roster;
    }

    public function setRoster(string $roster): self
    {
        $this->roster = $roster;

        return $this;
    }

    /**
     * @return Collection|Player[]
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    /**
     * @return Collection|Player[]
     */
    public function getNotDeadPlayers(): Collection
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('dead', false))
            ->orderBy(['number' => 'ASC'])
        ;
        return $this->players->matching($criteria);
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $player->setTeam($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->contains($player)) {
            $this->players->removeElement($player);
            // set the owning side to null (unless already changed)
            if ($player->getTeam() === $this) {
                $player->setTeam(null);
            }
        }

        return $this;
    }

    /*public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new Assert\Callback('validateRule'));
    }

    public function validateRule(ExecutionContextInterface $context, $payload)
    {
        if (!($context->getValue()->getRule() || $context->getValue()->getChampionship()) ||
            $context->getValue()->getRule() && $context->getValue()->getChampionship()) {
            $context->buildViolation('obblm.constraints.team.rule_or_championship.violation')
                ->addViolation();
        }
    }*/

    public function getReady(): ?bool
    {
        return $this->ready;
    }

    public function isReady(): ?bool
    {
        return $this->getReady();
    }

    public function setReady(bool $ready): self
    {
        $this->ready = $ready;

        return $this;
    }

    public function getLockedByManagment(): ?bool
    {
        return $this->locked_by_managment;
    }

    public function isLockedByManagment(): ?bool
    {
        return $this->getLockedByManagment();
    }

    public function setLockedByManagment(bool $locked_by_managment): self
    {
        $this->locked_by_managment = $locked_by_managment;

        return $this;
    }

    /**
     * @return Collection|TeamVersion[]
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(TeamVersion $version): self
    {
        if (!$this->versions->contains($version)) {
            $this->versions[] = $version;
            $version->setTeam($this);
        }

        return $this;
    }

    public function removeVersion(TeamVersion $version): self
    {
        if ($this->versions->contains($version)) {
            $this->versions->removeElement($version);
            // set the owning side to null (unless already changed)
            if ($version->getTeam() === $this) {
                $version->setTeam(null);
            }
        }

        return $this;
    }
}
