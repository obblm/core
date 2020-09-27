<?php

namespace Obblm\Core\Controller;

use Obblm\Core\Entity\Team;
use Obblm\Core\Service\FileTeamUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AssetsController
 * @package Obblm\Core\Controller
 *
 * @Route("/assets", name="assets")
 */
class AssetsController extends AbstractController
{
    /**
     * @Route("/team/{team}/logo", name="_team_logo")
     */
    public function teamLogo(FileTeamUploader $uploader, Team $team)
    {
        if ($team->getLogoFilename()) {
            $uploader->setObjectSubDirectory($team->getId());
            $file = new File($uploader->getObjectDirectory() . '/' . $team->getLogoFilename());

            return $this->file($file, null, ResponseHeaderBag::DISPOSITION_INLINE);
        }
        throw $this->createNotFoundException();
    }
    /**
     * @Route("/team/{team}/cover", name="_team_cover")
     */
    public function teamCover(FileTeamUploader $uploader, Team $team)
    {
        if ($team->getCoverFilename()) {
            $uploader->setObjectSubDirectory($team->getId());
            $file = new File($uploader->getObjectDirectory() . '/' . $team->getCoverFilename());

            return $this->file($file, null, ResponseHeaderBag::DISPOSITION_INLINE);
        }
        throw $this->createNotFoundException();
    }
}
