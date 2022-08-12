<?php

namespace Obblm\Core\Domain\Contracts;

interface ObblmFileUploaderInterface
{
    public function upload($file): ?\SplFileInfo;

    public function remove(string $filename);

    public function getTargetDirectory(): string;

    public function getObjectDirectory(): string;

    public function setObjectSubDirectory(string $uploadDirectory): self;
}
