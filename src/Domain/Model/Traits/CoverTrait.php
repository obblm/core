<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Model\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CoverTrait
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $coverFilename;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $coverMimeType;

    public function getCoverFilename(): ?string
    {
        return $this->coverFilename;
    }

    public function setCoverFilename(?string $coverFilename): self
    {
        $this->coverFilename = $coverFilename;

        return $this;
    }

    public function getCoverMimeType(): ?string
    {
        return $this->coverMimeType;
    }

    public function setCoverMimeType(?string $coverMimeType): self
    {
        $this->coverMimeType = $coverMimeType;

        return $this;
    }
}
