<?php

namespace Obblm\Core\Service;

use Obblm\Core\Contracts\BuildAssetsInterface;

class CoreBuildAssets implements BuildAssetsInterface
{
    public function getPath(): string
    {
        return dirname(__DIR__) . '/Resources/public/build';
    }
}
