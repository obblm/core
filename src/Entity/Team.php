<?php

namespace Obblm\Core\Entity;

use Obblm\Core\Entity\Traits\CoverTrait;
use Obblm\Core\Entity\Traits\LogoTrait;
use Obblm\Core\Entity\Traits\NameTrait;
use Obblm\Core\Entity\Traits\RuleTrait;
use Obblm\Core\Entity\Traits\TimeStampableTrait;
use Obblm\Core\Helper\Rule\CanHaveRuleInterface;
use Obblm\Core\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 * @ORM\Table(name="obblm_team")
 * @ORM\HasLifecycleCallbacks
 */
class Team implements CanHaveRuleInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    use NameTrait, RuleTrait, LogoTrait, CoverTrait, TimeStampableTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Coach::class, inversedBy="teams")
     * @ORM\JoinColumn(nullable=false)
     */
    private $coach;

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
     * @ORM\Column(type="array", nullable=true)
     */
    private $creationOptions;

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
    private $lockedByManagment = false;

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

    public function getCoach(): ?Coach
    {
        return $this->coach;
    }

    public function setCoach(?Coach $coach): self
    {
        $this->coach = $coach;

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

    public function getCreationOptions(): ?array
    {
        return $this->creationOptions;
    }

    public function setCreationOptions(array $creationOptions): self
    {
        $this->creationOptions = $creationOptions;
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
    public function getAvailablePlayers(): Collection
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('dead', false))
            ->andWhere(Criteria::expr()->eq('fire', false))
            ->orderBy(['number' => 'ASC'])
        ;
        return $this->players->matching($criteria);
    }

    public function getAvailablePlayersSheet(): Collection
    {
        // In want to have 16 players in the list, no less, no more
        $usedNumbers = [];
        $newPlayerList = $this->getAvailablePlayers();
        foreach ($newPlayerList as $player) {
            $usedNumbers[$player->getNumber()] = $player;
        }
        for ($i=1; $i<=16; $i++) {
            if (!isset($usedNumbers[$i])) {
                $newPlayerList->add((new Player())->setNumber($i));
            }
        }
        $criteria = Criteria::create();
        $criteria->orderBy(['number' => 'ASC']);
        return $newPlayerList->matching($criteria);
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
        return $this->lockedByManagment;
    }

    public function isLockedByManagment(): ?bool
    {
        return $this->getLockedByManagment();
    }

    public function setLockedByManagment(bool $lockedByManagment): self
    {
        $this->lockedByManagment = $lockedByManagment;

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
