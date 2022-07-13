<?php

declare(strict_types=1);

namespace Obblm\Core\Application\Service;

use Obblm\Core\Domain\Contracts\BuildAssetsInterface;

class CoreBuildAssets implements BuildAssetsInterface
{
    public function getPath(): string
    {
        return dirname(__DIR__).'/Resources/public/build';
    }
}
