<?php

namespace Obblm\Core\Helper\FileUploader;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class AbstractFileUploader
{
    private $targetDirectory;
    private $uploadDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->uploadDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function removeOldFile(string $filename = '')
    {
        $filesystem = new Filesystem();

        try {
            $filesystem->remove($this->getObjectDirectory() . '/' . $filename);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at ".$exception->getPath();
        }
    }

    public function getTargetDirectory(): ?string
    {
        return $this->targetDirectory;
    }

    public function getObjectDirectory(): ?string
    {
        return $this->uploadDirectory;
    }

    public function setObjectSubDirectory(string $uploadDirectory): self
    {
        $this->uploadDirectory = $this->targetDirectory .'/' . $uploadDirectory;
        return $this;
    }

    /**
     * @param UploadedFile $file
     * @return File|null
     */
    public function upload(UploadedFile $file):?File
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $newFile = $file->move($this->getObjectDirectory(), $fileName);
        } catch (FileException $e) {
            return null;
        }

        return $newFile;
    }
}
