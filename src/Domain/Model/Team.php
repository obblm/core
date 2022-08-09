<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Obblm\Core\Domain\Model\Traits\CoverTrait;
use Obblm\Core\Domain\Model\Traits\LogoTrait;
use Obblm\Core\Domain\Model\Traits\NameTrait;
use Obblm\Core\Domain\Model\Traits\RuleTrait;
use Obblm\Core\Domain\Model\Traits\TimeStampableTrait;
use Symfony\Component\Uid\Uuid;

class Team
{
    use NameTrait;
    use RuleTrait;
    use LogoTrait;
    use CoverTrait;
    use TimeStampableTrait;
    private $id;
    private Coach $coach;
    private ?string $anthem = null;
    private ?string $fluff = null;
    private string $roster;
    private array $creationOptions;
    private Collection $players;
    private bool $ready = false;
    private bool $lockedByManagment = false;
    private Collection $versions;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->versions = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(Uuid $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getCoach(): Coach
    {
        return $this->coach;
    }

    public function setCoach(Coach $coach): self
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

    /**
     * @return mixed|null
     */
    public function getCreationOption(string $key)
    {
        return $this->getOption($this->creationOptions, $key);
    }

    private function getOption($on, $key)
    {
        $keys = explode('.', $key, 2);
        $key = $keys[0];
        if (isset($on[$key])) {
            if (!isset($keys[1])) {
                return $on[$key];
            }
            if (isset($keys[1])) {
                return $this->getOption($on[$key], $keys[1]);
            }
        }

        return null;
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
        for ($i = 1; $i <= 16; ++$i) {
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
