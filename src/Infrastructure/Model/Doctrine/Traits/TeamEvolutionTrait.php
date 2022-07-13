<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Model\Doctrine\Traits;

use Doctrine\ORM\Mapping as ORM;

trait TeamEvolutionTrait
{
    /**
     * @ORM\Column(type="integer")
     */
    private $points = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $tdGive = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $tdTake = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $injuryGive = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $injuryTake = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $gameWin = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $gameDraw = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $gameLoss = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $tr = 0;

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getTdGive(): ?int
    {
        return $this->tdGive;
    }

    public function setTdGive(int $tdGive): self
    {
        $this->tdGive = $tdGive;

        return $this;
    }

    public function getTdTake(): ?int
    {
        return $this->tdTake;
    }

    public function setTdTake(int $tdTake): self
    {
        $this->tdTake = $tdTake;

        return $this;
    }

    public function getInjuryGive(): ?int
    {
        return $this->injuryGive;
    }

    public function setInjuryGive(int $injuryGive): self
    {
        $this->injuryGive = $injuryGive;

        return $this;
    }

    public function getInjuryTake(): ?int
    {
        return $this->injuryTake;
    }

    public function setInjuryTake(int $injuryTake): self
    {
        $this->injuryTake = $injuryTake;

        return $this;
    }

    public function getGameWin(): ?int
    {
        return $this->gameWin;
    }

    public function setGameWin(int $gameWin): self
    {
        $this->gameWin = $gameWin;

        return $this;
    }

    public function getGameDraw(): ?int
    {
        return $this->gameDraw;
    }

    public function setGameDraw(int $gameDraw): self
    {
        $this->gameDraw = $gameDraw;

        return $this;
    }

    public function getGameLoss(): ?int
    {
        return $this->gameLoss;
    }

    public function setGameLoss(int $gameLoss): self
    {
        $this->gameLoss = $gameLoss;

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
}
