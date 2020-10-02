<?php

namespace Obblm\Core\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait LogoTrait
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $logoFilename;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $logoMimeType;

    public function getLogoFilename(): ?string
    {
        return $this->logoFilename;
    }

    public function setLogoFilename(?string $logoFilename): self
    {
        $this->logoFilename = $logoFilename;
        return $this;
    }

    public function getLogoMimeType(): ?string
    {
        return $this->logoMimeType;
    }

    public function setLogoMimeType(?string $logoMimeType): self
    {
        $this->logoMimeType = $logoMimeType;
        return $this;
    }
}
