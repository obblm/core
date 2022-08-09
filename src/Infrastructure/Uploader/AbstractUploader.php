<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Uploader;

use Obblm\Core\Domain\Contracts\ObblmFileUploaderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractUploader implements ObblmFileUploaderInterface
{
    private string $targetDirectory = '';
    private string $uploadDirectory = '';

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

    abstract public function upload(UploadedFile $file): ?File;

    abstract public function remove(string $filename);
}
