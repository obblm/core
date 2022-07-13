<?php

namespace Obblm\Core\Domain\Helper;

use Obblm\Core\Application\Service\FileUploader\AbstractFileUploader;
use Obblm\Core\Application\Service\FileUploader\FileUploaderInterface;
use Obblm\Core\Domain\Model\Team;
use Symfony\Component\HttpFoundation\File\File;

class FileTeamUploader extends AbstractFileUploader implements FileUploaderInterface
{
    /**
     * @param $object
     * @param $field
     * @param $file
     *
     * @return File|null
     */
    public function uploadIfExists($object, $file, $field)
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

    public function uploadLogoIfExists(Team $object, $logoFile): ?File
    {
        if ($logoFile) {
            $this->setObjectSubDirectory($object->getId());
            if ($logo = $this->upload($logoFile)) {
                if ($object->getLogoFilename()) {
                    $this->removeOldFile($object->getLogoFilename());
                }
                $object->setLogoFilename($logo->getFilename());
                $object->setLogoMimeType($logo->getMimeType());

                return $logo;
            }
        }

        return null;
    }

    public function uploadCoverIfExists(Team $object, $coverFile): ?File
    {
        if ($coverFile) {
            $this->setObjectSubDirectory($object->getId());
            if ($cover = $this->upload($coverFile)) {
                if ($object->getCoverFilename()) {
                    $this->removeOldFile($object->getCoverFilename());
                }
                $object->setCoverFilename($cover->getFilename());
                $object->setCoverMimeType($cover->getMimeType());

                return $cover;
            }
        }

        return null;
    }
}
