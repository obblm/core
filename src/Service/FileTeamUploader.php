<?php

namespace Obblm\Core\Service;

use Obblm\Core\Entity\Team;
use Obblm\Core\Helper\FileUploader\AbstractFileUploader;
use Obblm\Core\Helper\FileUploader\FileUploaderInterface;
use Symfony\Component\HttpFoundation\File\File;

class FileTeamUploader extends AbstractFileUploader implements FileUploaderInterface
{
    /**
     * @param $object
     * @param $field
     * @param $file
     * @return File|null
     */
    public function uploadIfExists($object, $file, $field)
    {
        if (!$object instanceof Team || !$file) {
            return null;
        }
        if ($field === 'logo') {
            return $this->uploadLogoIfExists($object, $file);
        }
        if ($field === 'cover') {
            return $this->uploadCoverIfExists($object, $file);
        }
        return null;
    }

    public function uploadLogoIfExists(Team $object, $logoFile):?File
    {
        if($logoFile) {
            $this->setObjectSubDirectory($object->getId());
            if ($logo = $this->upload($logoFile))
            {
                if($object->getLogoFilename()) {
                    $this->removeOldFile($object->getLogoFilename());
                }
                $object->setLogoFilename($logo->getFilename());
                $object->setLogoMimeType($logo->getMimeType());
                return $logo;
            }
        }
        return null;
    }
    public function uploadCoverIfExists(Team $object, $coverFile):?File
    {
        if($coverFile) {
            $this->setObjectSubDirectory($object->getId());
            if ($cover = $this->upload($coverFile))
            {
                if($object->getCoverFilename()) {
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
