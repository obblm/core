<?php

namespace Obblm\Core\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SidelinesTrait
{
    /**
     * @ORM\Column(type="integer")
     */
    private $rerolls;

    /**
     * @ORM\Column(type="integer")
     */
    private $cheerleaders;

    /**
     * @ORM\Column(type="integer")
     */
    private $assistants;

    /**
     * @ORM\Column(type="integer")
     */
    private $popularity;

    /**
     * @ORM\Column(type="boolean")
     */
    private $apothecary;

    public function getRerolls(): ?int
    {
        return $this->rerolls;
    }

    public function setRerolls(int $rerolls): self
    {
        $this->rerolls = $rerolls;

        return $this;
    }

    public function getCheerleaders(): ?int
    {
        return $this->cheerleaders;
    }

    public function setCheerleaders(int $cheerleaders): self
    {
        $this->cheerleaders = $cheerleaders;

        return $this;
    }

    public function getAssistants(): ?int
    {
        return $this->assistants;
    }

    public function setAssistants(int $assistants): self
    {
        $this->assistants = $assistants;

        return $this;
    }

    public function getPopularity(): ?int
    {
        return $this->popularity;
    }

    public function setPopularity(int $popularity): self
    {
        $this->popularity = $popularity;

        return $this;
    }

    public function getApothecary(): ?bool
    {
        return $this->apothecary;
    }

    public function setApothecary(bool $apothecary): self
    {
        $this->apothecary = $apothecary;

        return $this;
    }
}
