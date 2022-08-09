<?php

declare(strict_types=1);

namespace Obblm\Core\Infrastructure\Uploader;

use Obblm\Core\Domain\Contracts\ObblmFileUploaderInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class LocalFileUploader extends AbstractUploader implements ObblmFileUploaderInterface
{
    private string $targetDirectory = '';
    private string $uploadDirectory = '';

    private SluggerInterface $slugger;

    public function __construct(string $targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->uploadDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function removeOldFile(string $filename = '')
    {
        $this->remove($filename);
    }

    public function remove(string $filename)
    {
        $filesystem = new Filesystem();
        try {
            $filesystem->remove($this->getObjectDirectory().'/'.$filename);
        } catch (IOExceptionInterface $exception) {
            echo 'An error occurred while creating your directory at '.$exception->getPath();
        }
    }

    public function upload(UploadedFile $file): ?File
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
