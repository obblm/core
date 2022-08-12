<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Uploader;

use Obblm\Core\Domain\Contracts\ObblmFileUploaderInterface;
use Symfony\Component\HttpFoundation\File\File;

abstract class AbstractUploader implements ObblmFileUploaderInterface
{
    protected string $targetDirectory = '';
    protected string $uploadDirectory = '';

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function getObjectDirectory(): string
    {
        return $this->uploadDirectory;
    }

    public function setObjectSubDirectory(string $uploadDirectory): self
    {
        $this->uploadDirectory = $this->targetDirectory.'/'.$uploadDirectory;

        return $this;
    }

    abstract public function upload($file): ?File;

    abstract public function remove(string $filename);
}
