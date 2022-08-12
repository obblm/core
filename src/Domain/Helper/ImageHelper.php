<?php

namespace Obblm\Core\Domain\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Intervention\Image\ImageManager;
use Obblm\Core\Domain\Model\Rule;
use Obblm\Core\Domain\Model\Team;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

class ImageHelper
{
    const RULE_PATH = '/rule';
    const TEAM_PATH = '/team';

    private $imageManager;
    private $ruleHelper;
    private $publicDirectory;
    private $cacheDirectory;
    private $ruleCacheDirectory;
    private $teamCacheDirectory;
    private $filesystem;
    private $fileTeamUploader;

    public function __construct(FileTeamUploader $fileTeamUploader, string $kernetProjectDir = '', string $cacheDirectory = '')
    {
        $this->imageManager = new ImageManager(['driver' => 'gd']);
        $this->publicDirectory = $kernetProjectDir;
        $this->cacheDirectory = $cacheDirectory;
        $this->ruleCacheDirectory = $this->cacheDirectory.self::RULE_PATH;
        $this->teamCacheDirectory = $this->cacheDirectory.self::TEAM_PATH;
        $this->filesystem = new Filesystem();
        $this->fileTeamUploader = $fileTeamUploader;
    }

    public function getTeamLogo(Team $team, int $width = null, int $height = null): ?string
    {
        if ($team->getLogoFilename()) {
            $this->fileTeamUploader->setObjectSubDirectory($team->getId());
            $filePath = $this->fileTeamUploader->getObjectDirectory().'/'.$team->getLogoFilename();
            if ($width || $height) {
                $newFilePath = $this->teamCacheDirectory.'/'.$team->getId();
                $file = new File($filePath);

                return $this->returnCachedAsset($file, $newFilePath, $width, $height);
            }
            // full sized
            return $this->sanitizeFilePath($filePath);
        }

        return $this->getRosterImage($team->getRule(), $team->getRoster(), $width, $height);
    }

    public function getTeamCover(Team $team, int $width = null, int $height = null): ?string
    {
        if ($team->getCoverFilename()) {
            $this->fileTeamUploader->setObjectSubDirectory($team->getId());
            $filePath = $this->fileTeamUploader->getObjectDirectory().'/'.$team->getCoverFilename();
            if ($width || $height) {
                $newFilePath = $this->teamCacheDirectory.'/'.$team->getId();
                $file = new File($filePath);

                return $this->returnCachedAsset($file, $newFilePath, $width, $height);
            }
            // full sized
            return $this->sanitizeFilePath($filePath);
        }

        return null;
    }

    public function getRosterImage(Rule $rule, string $roster, int $width = null, int $height = null): ?string
    {
        /*$helper = $this->ruleHelper->getHelper($rule);
        if ($helper->getRosters()->get($roster) && $rule->getRuleDirectory()) {
            $finder = new Finder();
            $directory = dirname(__DIR__).$rule->getRuleDirectory().'/assets/';
            $finder->files()
                ->name($roster.'.*')
                ->in($directory);
            if ($finder->hasResults()) {
                $results = new ArrayCollection();
                foreach ($finder as $file) {
                    $results->add($file);
                }
                // We have to resize
                $newFilePath = $this->ruleCacheDirectory.'/'.$rule->getId();
                $file = $results->first();

                return $this->returnCachedAsset($file, $newFilePath, $width, $height);
            }
        }*/

        return null;
    }

    private function returnCachedAsset(SplFileInfo $file, $path = '', $width = null, $height = null)
    {
        if ($width) {
            $path .= '/w'.$width;
        }
        if ($height) {
            $path .= '/h'.$height;
        }
        $newCacheDirectory = $path;
        $path .= '/'.$file->getFilename();
        if (!$this->filesystem->exists($path)) {
            $img = $this->imageManager->make($file);
            $this->filesystem->mkdir($newCacheDirectory);
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($path);
        }

        return $this->sanitizeFilePath($path);
    }

    private function sanitizeFilePath($filePath)
    {
        return str_replace($this->publicDirectory, '', $filePath);
    }
}
