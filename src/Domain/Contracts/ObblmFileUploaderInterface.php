<?php

namespace Obblm\Core\Domain\Contracts;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ObblmFileUploaderInterface
{
    public function upload(UploadedFile $file): ?File;

    public function remove(string $filename);

    public function getTargetDirectory(): string;

    public function getObjectDirectory(): string;

    public function setObjectSubDirectory(string $uploadDirectory): self;
}
