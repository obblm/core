<?php

declare(strict_types=1);

namespace Obblm\Core\Domain\Helper;

use Obblm\Core\Domain\Contracts\ObblmFileUploaderInterface;
use Obblm\Core\Domain\Model\Team;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileTeamUploader
{
    private ?ObblmFileUploaderInterface $uploader = null;

    public function setUploader(ObblmFileUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param Team $object
     * @param string $field
     * @param UploadedFile $file
     *
     * @return File|null
     */
    public function uploadIfExists(Team $object, UploadedFile $file, string $field)
    {
        if (!$object instanceof Team || !$file) {
            return null;
        }
        if ('logo' === $field) {
            return $this->uploadLogoIfExists($object, $file);
        }
        if ('cover' === $field) {
            return $this->uploadCoverIfExists($object, $file);
        }

        return null;
    }

    public function uploadLogoIfExists(Team $object, UploadedFile $logoFile): ?File
    {
        if ($logoFile) {
            $this->uploader->setObjectSubDirectory((string) $object->getId());
            if ($logo = $this->uploader->upload($logoFile)) {
                if ($object->getLogoFilename()) {
                    $this->uploader->remove($object->getLogoFilename());
                }
                $object->setLogoFilename($logo->getFilename());
                $object->setLogoMimeType($logo->getMimeType());

                return $logo;
            }
        }

        return null;
    }

    public function uploadCoverIfExists(Team $object, UploadedFile $coverFile): ?File
    {
        if ($coverFile) {
            $this->uploader->setObjectSubDirectory((string) $object->getId());
            if ($cover = $this->uploader->upload($coverFile)) {
                if ($object->getCoverFilename()) {
                    $this->uploader->remove($object->getCoverFilename());
                }
                $object->setCoverFilename($cover->getFilename());
                $object->setCoverMimeType($cover->getMimeType());

                return $cover;
            }
        }

        return null;
    }
}
