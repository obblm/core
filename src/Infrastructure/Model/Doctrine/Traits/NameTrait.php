<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Model\Doctrine\Traits;

use Doctrine\ORM\Mapping as ORM;

trait NameTrait
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->getName();
    }
}
