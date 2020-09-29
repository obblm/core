<?php

namespace Obblm\Core\Service;

use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class ObblmPackage
{
    private $package;

    public function __construct($kernetProjectDir, $uploadDirectory, $publicUploadUrl = null)
    {
        $directory = str_replace($kernetProjectDir, '', $uploadDirectory);
        $this->package = new PathPackage($directory, new EmptyVersionStrategy());
    }

    public function getUrl(string $path)
    {
        return $this->package->getUrl($path);
    }
}
